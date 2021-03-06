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
		global $config, $db, $user, $template, $phpbb_container, $request;

		$start	= $request->variable('start', 0);
		$fts	= $request->variable('ft', 1);
		$action	= $request->variable('action', '');

		switch ($action)
		{
			case 'details':
				$user->add_lang_ext('forumhulp/forumtopicwatch', 'info_acp_forumtopicwatch');
				$phpbb_container->get('forumhulp.helper')->detail('forumhulp/forumtopicwatch');
				$this->tpl_name = 'acp_ext_details';
			break;

			default:
				$sql = 'SELECT COUNT(DISTINCT user_id) AS total_users FROM ' . (($fts) ? FORUMS_WATCH_TABLE : TOPICS_WATCH_TABLE);
				$result = $db->sql_query($sql);
				$total_users =  (int) $db->sql_fetchfield('total_users');
				$db->sql_freeresult($result);

				if (!$total_users)
				{
					$sql = 'SELECT COUNT(DISTINCT user_id) AS total_users FROM ' . (($fts) ? TOPICS_WATCH_TABLE : FORUMS_WATCH_TABLE);
					$result = $db->sql_query($sql);
					$total_users =  (int) $db->sql_fetchfield('total_users');
					$db->sql_freeresult($result);

					$fts = ($total_users) ? 0 : 1;
				}

				$config['posts_per_page'] = 10;
				if ($start < 0 || $start >= $total_users)
				{
					$start = ($start < 0) ? 0 : floor(($total_users - 1) / $config['posts_per_page']) * $config['posts_per_page'];
				}

				$sql = 'SELECT w.*, GROUP_CONCAT(' . (($fts) ? 'f.forum_name' : 'f.topic_title') . ' ORDER BY ' . (($fts) ? 'f.forum_name' :
						'f.topic_title') . ' SEPARATOR ", ") AS forums, u.username 
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
					'S_ON_PAGE'		=> ($total_users) ? $pagination->on_page($total_users, $config['posts_per_page'], $start) : '',
					'S_FT'			=> $fts,
					'U_FORUMWATCH'	=> $this->u_action,
					'U_TOPICWATCH'	=> str_replace('ft=1', 'ft=0' , (strpos('ft=0', $this->u_action) === false) ? $this->u_action . '&ft=0' : $this->u_action),
					'U_ACTION'		=> $this->u_action
					)
				);
				$this->tpl_name = 'acp_forum_topicwatch';
				$this->page_title = 'ACP_FORUM_TOPICWATCH';
		}
	}
}
