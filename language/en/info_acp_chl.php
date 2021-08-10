<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2018 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters for use
// ’ » “ ” …

$lang = array_merge($lang, [
	'ACP_CHI_FORUMS_TITLE'				=> 'Forums &amp; categories logo assignment',
	'ACP_CHI_PAGES_TITLE'				=> 'Custom page logo assignment',
	'ACP_CHI_SETTINGS_TITLE'			=> 'Basic settings',
	'ACP_CHI_TITLE'						=> 'Custom Header Logo',
	// Log entries
	'LOG_CHI_SETTINGS_UPDATED'			=> '<strong>Custom Header Logo settings updated</strong>',
	'LOG_CHI_ADDED'						=> '<strong>Custom Header Logo entry added</strong>',
	'LOG_CHI_UPDATED'					=> '<strong>Custom Header Logo entry updated</strong>',
	'LOG_CHI_DELETED'					=> '<strong>Custom Header Logo entry deleted</strong>',
]);
