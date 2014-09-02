<?php
/**
*
* @package Forum- Topicwatch
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @package module_install
*/

namespace forumhulp\forumtopicwatch\acp;

class forumtopicwatch_info
{
	function module()
	{
		return array(
			'filename'	=> '\forumhulp\forumtopicwatch\acp\forum_topicwatch_module',
			'title'		=> 'ACP_FORUM_TOPICWATCH',
			'version'	=> '3.1.0',
			'modes'     => array('index' => array('title' => 'ACP_FORUM_TOPICWATCH', 'auth' => 'acl_a_board', 'cat' => array('ACP_FORUM_LOGS')),
			),
		);
	}
}

