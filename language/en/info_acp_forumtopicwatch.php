<?php
/**
*
* @package Forum- Topicwatch
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_FORUM_TOPICWATCH'	=> 'Forum- TopicWatch',
	'ACP_FORUM_TOPICWATCH_EXPLAIN'	=> 'Watch your users and their subscribed forums / topics. Switch between forums or topics to see different results.',
	'FH_HELPER_NOTICE'		=> 'Forumhulp helper application does not exist!<br />Download <a href="https://github.com/ForumHulp/helper" target="_blank">forumhulp/helper</a> and copy the helper folder to your forumhulp extension folder.',
	'FTWATCH_NOTICE'		=> '<div class="phpinfo"><p class="entry">This extension resides in %1$s » %2$s » %3$s.</p></div>',
));

// Description of Donations extension
$lang = array_merge($lang, array(
	'DESCRIPTION_PAGE'		=> 'Description',
	'DESCRIPTION_NOTICE'	=> 'Extension note',
	'ext_details' => array(
		'details' => array(
			'DESCRIPTION_1'	=> 'Overview of forumwatches',
			'DESCRIPTION_2'	=> 'Overview of topicwatches',
		),
		'note' => array(
			'NOTICE_1'		=> 'phpBB 3.2 ready',
		)
	)
));
