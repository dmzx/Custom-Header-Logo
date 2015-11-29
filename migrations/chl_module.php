<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\migrations;

class chl_module extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			//Add config
			array('config.add', array('chi_enable', 0)),
			array('config.add', array('chi_enable_guests', 0)),

			//Add module
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_CHI_TITLE')),
			array('module.add', array(
				'acp', 'ACP_CHI_TITLE', array(
				'module_basename'	=> '\dmzx\chl\acp\acp_chl_module', 'modes'	=> array('settings', 'forums', 'pages'),
				),
			)),
		);
	}
}

