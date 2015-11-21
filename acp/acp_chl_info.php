<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\acp;

class acp_chl_info
 {
	function module()
	{
		 return array(
			'filename'	=> '\dmzx\chl\acp\acp_chl_module',
			'title'		=> 'ACP_CHI_TITLE',
			'modes'		=> array(
				'settings'	=> array('title'	=> 'ACP_CHI_SETTINGS_TITLE',	'auth'	=> 'ext_dmzx/chl && acl_a_board', 'cat'	=> array('ACP_BOARD_CONFIGURATION')),
				'forums'	=> array('title'	=> 'ACP_CHI_FORUMS_TITLE',		'auth'	=> 'ext_dmzx/chl && acl_a_board', 'cat'	=> array('ACP_BOARD_CONFIGURATION')),
				'pages'		=> array('title'	=> 'ACP_CHI_PAGES_TITLE',		'auth'	=> 'ext_dmzx/chl && acl_a_board', 'cat'	=> array('ACP_BOARD_CONFIGURATION')),
			),
		);
	}
}