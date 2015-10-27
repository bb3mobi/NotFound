<?php
/**
*
* NOT_FOUND [English]
*
* @package info_acp_notfound.php
* @copyright Anvar apwa.ru (c) 2015 http://bb3.mobi
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
	'ACP_NOT_FOUND'					=> 'Error 404',
	'ACP_NOT_FOUND_TITLE'			=> 'The document or file not found!',
	'ACP_NOT_FOUND_EXPLAIN'			=> 'Here is an opportunity to set the text\error description',
	'ACP_NOT_FOUND_TEXT'			=> 'Text of error',
	'ACP_NOT_FOUND_TEXT_EXPLAIN'	=> 'To format a page, you can use html',

	'NOT_FOUND_AUTHOR'		=> '&copy; <a href="http://bb3.mobi/forum/" title="Development Extension">phpBB3.1 Extension</a>',
));
