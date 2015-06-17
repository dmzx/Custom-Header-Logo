<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* Translation by Holger (https://www.maskinisten.net)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
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
	'FORUM_LOGO' 						=> 'Forumlogga',
	'NO_LOGO' 							=> 'Ingen egen logga',
	'LOGO_PATH' 						=> 'Sökväg för forumloggor',
	'LOGO_PATH_EXPLAIN' 				=> 'Sökväg under din phpBB root-katalog, t.ex. <samp>images/logos</samp>.',
	'FORUM_BACKGROUND'					=> 'Forumhuvud bakgrund',
	'BACKGROUND_PATH'					=> 'Sökväg för bakgrundbilder för forumhuvud',
	'BACKGROUND_PATH_EXPLAIN' 			=> 'Sökväg under din phpBB root-katalog, t.ex. <samp>images/backgrounds</samp>.',
	'NO_BACKGROUND_LOGO' 				=> 'Ingen egen bakgrundsbild',

	'CHI_TITLE_ADD'						=> 'Lägg till nytt',
	'CHI_TITLE_EDIT'					=> 'Redigera ett',
	'CHI_TITLE_PAGE'					=> 'par av sida och logga/bakgrundsbild',
	'CHI_TITLE_FORUM'					=> 'par av forum/kategori och logga/bakgrundsbild',
	'CHI_NEED_PAGE'						=> 'Minst en sida och logga eller bakgrundsbild krävs',
	'CHI_NEED_FORUM'					=> 'Minst ett forum och logga eller bakgrundsbild krävs',
	'CHI_FORUM_FORBIDDEN'				=> 'Du kan ej lägga till forum här, använd den andra sidan till att matcha ett forum eller en kategori till en logga!',
	'CHI_ADDED'							=> 'Egen logga har lagts till',

	'CHI_UPDATED'						=> 'Posten har aktualiserats',
	'CHI_DELETED'						=> 'Posten har raderats',
	'CHI_REALY_DELETE'					=> 'Vill du verkligen radera posten?',
	'CHI_PAGENAME'						=> 'Sidans namn',
	'CHI_PAGENAME_DESC'					=> 'Ange sidans namn utan filtillägg (t.ex. "search" eller "ucp")',
	'CHI_FORUM_DESC'					=> 'Notera: forum som redan har lagts till är deaktiverade. Redigera posten istället.',
	'CHI_LOGO'							=> 'Logga',
	'CHI_LOGO_DESC'						=> 'Välj logga från dropdownlistan',
	'CHI_BACKGROUND'					=> 'Bakgrundsbild',
	'CHI_BACKGROUND_DESC'				=> 'Välj bakgrundsbild från dropdownlistan',
	'CHI_PATH'							=> 'Sökväg till bilden',
	'CHI_PATH_DESC'						=> 'Ange sökväg till skriptet, t.ex. "gallery" (valfritt)',
	'CHI_QUERY'							=> 'Sidans query',
	'CHI_QUERY_DESC'					=> 'Ange ett query efter skriptnamnet, t.ex. "mode=leaders" eller "*" för att visa obereonde av query (valfritt). Query har högre prio än "*". Du behöver ej ange en komplett query, skriptet hanterar även delar av en query.',

	'ACP_CHI_DESC_FORUMS'				=> 'Här kan du lägga till, redigera eller radera egna loggor och bakgrundsbilder för dina kategorier och forum.',
	'ACP_CHI_DESC_PAGES'				=> 'Här kan du lägga till, redigera eller radera egna loggor och bakgrundsbilder för dina sidor.',
	'ACP_CHI_FORUMNAME'					=> 'Forumets/kategorins namn',

	'ACP_CHI_DESC_SETTINGS'				=> 'Här kan du utföra olika grundinställningar för detta tillägg.',
	'ACP_CHI_ENABLE'					=> 'Aktivera tillägget för egna bakgrundsbilder?',
	'ACP_CHI_ENABLE_GUESTS'				=> 'Aktivera egna bakgrundsbilder för gäster?',
	'ACP_CHI_INCLUDE_SUBFORUMS'			=> 'Skall en definerad egen logga/bakgrundsbild även gälla för underforum?',

	'ACP_CHI_TITLE'						=> 'Egna bakgrundsbilder',
	'ACP_CHI_FORUMS_TITLE'				=> 'Loggor för forum &amp; kategorier',
	'ACP_CHI_PAGES_TITLE'				=> 'Loggor för egna sidor',
	'ACP_CHI_SETTINGS_TITLE'			=> 'Grundinställningar',

));

