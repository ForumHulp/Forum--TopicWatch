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

$revision = 'v3.1.0';
$name = 'Forum- topicwatch ' . $revision;

$lang = array_merge($lang, array(
	'ACP_FORUM_TOPICWATCH'	=> 'Forum- TopicWatch',
	
	'ACP_FORUM_TOPICWATCH_EXPLAIN'	=> 'Watch your users and their subscribed forums / topics. Switch between forums or topics to see different results.',
));

?>
