<?php
/**
*
* NOT_FOUND [Russian]
*
* @package info_acp_notfound.php
* @copyright (c) 2014 Anvar http://bb3.mobi
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
	'ACP_NOT_FOUND'			=> 'Ошибка 404',
	'ACP_NOT_FOUND_EXPLAIN'	=> 'Страница которая будет генерироваться в случае если она не существует.',
	'ACP_NOT_FOUND_TITLE'	=> 'Документ или файл не найден!',
	'ACP_NOT_FOUND_TEXT'			=> 'Текст ошибки',
	'ACP_NOT_FOUND_TEXT_EXPLAIN'	=> 'Для форматирования страницы можно использовать html',

	'NOT_FOUND_AUTHOR'		=> '&copy; <a href="http://bb3.mobi/forum/" title="Разработка расширений для форумов">Расширения для phpBB3.1</a>',
));
