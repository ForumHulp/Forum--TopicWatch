<?php
/**
*
* @package Forum- Topicwatch
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\forumtopicwatch\acp;

class forumtopicwatch_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx, $phpbb_container,  $phpbb_extension_manager, $request;

		$start	= $request->variable('start', 0);
		$fts	= $request->variable('ft', 1);
		$action	= $request->variable('action', '');

		switch ($action)
		{
			case 'details':

			$user->add_lang(array('install', 'acp/extensions', 'migrator'));
			$ext_name = 'forumhulp/forumtopicwatch';
			$md_manager = new \phpbb\extension\metadata_manager($ext_name, $config, $phpbb_extension_manager, $template, $user, $phpbb_root_path);
			try
			{
				$this->metadata = $md_manager->get_metadata('all');
			}
			catch(\phpbb\extension\exception $e)
			{
				trigger_error($e, E_USER_WARNING);
			}

			$md_manager->output_template_data();

			try
			{
				$updates_available = $this->version_check($md_manager, $request->variable('versioncheck_force', false));

				$template->assign_vars(array(
					'S_UP_TO_DATE'		=> empty($updates_available),
					'S_VERSIONCHECK'	=> true,
					'UP_TO_DATE_MSG'	=> $user->lang(empty($updates_available) ? 'UP_TO_DATE' : 'NOT_UP_TO_DATE', $md_manager->get_metadata('display-name')),
				));

				foreach ($updates_available as $branch => $version_data)
				{
					$template->assign_block_vars('updates_available', $version_data);
				}
			}
			catch (\RuntimeException $e)
			{
				$template->assign_vars(array(
					'S_VERSIONCHECK_STATUS'			=> $e->getCode(),
					'VERSIONCHECK_FAIL_REASON'		=> ($e->getMessage() !== $user->lang('VERSIONCHECK_FAIL')) ? $e->getMessage() : '',
				));
			}

			$template->assign_vars(array(
				'U_BACK'				=> $this->u_action . '&amp;action=list',
			));

			$this->tpl_name = 'acp_ext_details';
			break;

			default:
				$sql = 'SELECT COUNT(DISTINCT user_id) AS total_users FROM ' . (($fts) ? FORUMS_WATCH_TABLE : TOPICS_WATCH_TABLE);
				$result = $db->sql_query($sql);
				$total_users =  (int) $db->sql_fetchfield('total_users');
				$db->sql_freeresult($result);

				$config['posts_per_page'] = 10;
				if ($start < 0 || $start >= $total_users)
				{
					$start = ($start < 0) ? 0 : floor(($total_users - 1) / $config['posts_per_page']) * $config['posts_per_page'];
				}

				$sql = 'SELECT w.*, GROUP_CONCAT(' . (($fts) ? 'f.forum_name' : 'f.topic_title') . ' ORDER BY ' . (($fts) ? 'f.forum_name' : 'f.topic_title') . ' SEPARATOR ", ") AS forums, u.username 
						FROM ' . (($fts) ? FORUMS_WATCH_TABLE : TOPICS_WATCH_TABLE) . ' w
						LEFT JOIN ' . (($fts) ? FORUMS_TABLE : TOPICS_TABLE) . ' f ON ('.(($fts) ? 'f.forum_id = w.forum_id' : 'f.topic_id = w.topic_id') . ')
						LEFT JOIN ' . USERS_TABLE . ' u ON (u.user_id = w.user_id) 
						GROUP BY w.user_id ORDER BY u.username ASC';

				$result = $db->sql_query_limit($sql, $config['posts_per_page'], $start);
				while ($row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('forumwatch', array(
						'USERNAME'		=> $row['username'],
						'FORUMS'		=> $row['forums'],
						)
					);
				}

				$pagination = $phpbb_container->get('pagination');
				$base_url = $this->u_action . '&amp;ft=' . $fts;
				$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_users, $config['posts_per_page'], $start);

				// Generate page
				$template->assign_vars(array(
					'S_ON_PAGE'	=> ($total_users) ? $pagination->on_page($base_url, $total_users, $config['posts_per_page'], $start) : '',
					'THISURL'	=> $this->u_action,
					'U_ACTION'	=> $this->u_action
					)
				);
				$this->tpl_name = 'acp_forum_topicwatch';
				$this->page_title = 'ACP_FORUM_TOPICWATCH';
			}
		}
	/**
	* Check the version and return the available updates.
	*
	* @param \phpbb\extension\metadata_manager $md_manager The metadata manager for the version to check.
	* @param bool $force_update Ignores cached data. Defaults to false.
	* @param bool $force_cache Force the use of the cache. Override $force_update.
	* @return string
	* @throws RuntimeException
	*/
	protected function version_check(\phpbb\extension\metadata_manager $md_manager, $force_update = false, $force_cache = false)
	{
		global $cache, $config, $user;
		$meta = $md_manager->get_metadata('all');

		if (!isset($meta['extra']['version-check']))
		{
			throw new \RuntimeException($this->user->lang('NO_VERSIONCHECK'), 1);
		}

		$version_check = $meta['extra']['version-check'];

		$version_helper = new \phpbb\version_helper($cache, $config, $user);
		$version_helper->set_current_version($meta['version']);
		$version_helper->set_file_location($version_check['host'], $version_check['directory'], $version_check['filename']);
		$version_helper->force_stability($config['extension_force_unstable'] ? 'unstable' : null);

		return $updates = $version_helper->get_suggested_updates($force_update, $force_cache);
	}
}
