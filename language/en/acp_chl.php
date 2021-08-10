<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2018 dmzx - https://www.dmzx-web.net
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
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, [
	'FORUM_LOGO' 						=> 'Forum logo',
	'NO_LOGO' 							=> 'No custom logo',
	'LOGO_PATH' 						=> 'Forum logos storage path',
	'LOGO_PATH_EXPLAIN' 				=> 'Path under your phpBB root directory, e.g. <samp>images/chl_logos</samp>.',
	'FORUM_BACKGROUND'					=> 'Forum header background',
	'BACKGROUND_PATH'					=> 'Forum header background image storage path',
	'BACKGROUND_PATH_EXPLAIN' 			=> 'Path under your phpBB root directory, e.g. <samp>images/chl_backgrounds</samp>.',
	'NO_BACKGROUND_LOGO' 				=> 'No custom background image',
	'CHI_TITLE_ADD'						=> 'Add a new',
	'CHI_TITLE_EDIT'					=> 'Edit a',
	'CHI_TITLE_PAGE'					=> 'pair of page and logo/header background image',
	'CHI_TITLE_FORUM'					=> 'pair of forum/category and logo/header background image',
	'CHI_NEED_PAGE'						=> 'At least a page and logo or header background image is needed',
	'CHI_NEED_FORUM'					=> 'At least a forum and logo or header background image is needed',
	'CHI_FORUM_FORBIDDEN'				=> 'You cannot add forums here, please use the other page to match a forum or category to a logo!',
	'CHI_ADDED'							=> 'Custom Header Logo entry added',
	'CHI_UPDATED'						=> 'Custom Header Logo entry updated',
	'CHI_DELETED'						=> 'Custom Header Logo entry deleted',
	'CHI_SETTINGS_UPDATED'				=> 'Custom Header Logo settings updated',
	'CHI_REALY_DELETE'					=> 'Really delete entry?',
	'CHI_PAGENAME'						=> 'Page name',
	'CHI_PAGENAME_DESC'					=> 'Enter the name of the page without ending (e.g. “search” or “ucp”)',
	'CHI_FORUM_DESC'					=> 'Note: already added forums are disabled. Edit the entry instead.',
	'CHI_LOGO'							=> 'Logo',
	'CHI_LOGO_DESC'						=> 'Select logo from the dropdown list',
	'CHI_BACKGROUND'					=> 'Background image',
	'CHI_BACKGROUND_DESC'				=> 'Select header background image from the dropdown list',
	'CHI_PATH'							=> 'Path to the page',
	'CHI_PATH_DESC'						=> 'Enter the path to the script - e.g. “gallery” (optional)',
	'CHI_QUERY'							=> 'Page Query',
	'CHI_QUERY_DESC'					=> 'Enter the query after the script name - e.g. “mode=leaders” or “*” to display entry regardless of a set query string (optional). Query strings precede the “*”. You don’t have to enter the complete query string, the script will evaluate parts too.',
	'ACP_CHI_DESC_FORUMS'				=> 'Here you can add, edit and delete custom header logos and header background images for your categories and forums.',
	'ACP_CHI_DESC_PAGES'				=> 'Here you can add, edit and delete custom header logos and header background images for your custom pages.',
	'ACP_CHI_FORUMNAME'					=> 'Forum/category name',
	'ACP_CHI_DESC_SETTINGS'				=> 'Here you can configure various basic settings for the Custom Header Images extension.',
	'ACP_CHI_ENABLE'					=> 'Enable Custom Header Images',
	'ACP_CHI_ENABLE_GUESTS'				=> 'Enable Custom Header Images for guests',
	'ACP_CHI_INCLUDE_SUBFORUMS'			=> 'If a logo/header background is defined, use it for subforums too?',
	'ACP_CHI_WIDTH_SET'					=> 'Set logo width',
	'ACP_CHI_WIDTH_SET_EXPLAIN'			=> 'Set logo width in pixels.',
	'ACP_CHI_HEIGHT_SET'				=> 'Set logo height',
	'ACP_CHI_HEIGHT_SET_EXPLAIN'		=> 'Set logo height in pixels.',
	'ACP_CHI_SHOWPAGENAME'				=> 'Set show page name',
	'ACP_CHI_SHOWPAGENAME_EXPLAIN'		=> 'If enabled message is shown with page name.<br />Admin can see only. "Enable Custom Header Images" must be set to Yes.<br />Not visible in viewforum and viewtopic.',
	'ACP_CHI_VERSION'				    => 'Version',
]);
