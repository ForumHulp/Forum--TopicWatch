<?php
/**
*
* @package Forum- Topicwatch
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\forum_topicwatch\migrations;

class install_forum_topicwatch extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['forum_topicwatch_version']) && version_compare($this->config['forum_topicwatch_version'], '3.1.0', '>=');
	}

	static public function depends_on()
	{
		 return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_FORUM_LOGS',
				array(
					'module_basename'	=> '\forumhulp\forum_topicwatch\acp\forum_topicwatch_module',
					'module_langname'	=> 'ACP_FORUM_TOPICWATCH',
					'module_mode'		=> 'index'
				)
			)),

			array('config.add', array('forum_topicwatch_version', '3.1.0')),
		);
	}
}
