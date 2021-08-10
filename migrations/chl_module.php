<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2018 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\migrations;

class chl_module extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return [
			//Add config
			['config.add', ['chi_enable', 0]],
			['config.add', ['chi_enable_guests', 0]],

			//Add module
			['module.add', ['acp', 'ACP_CAT_DOT_MODS', 'ACP_CHI_TITLE']],
			['module.add', [
				'acp', 'ACP_CHI_TITLE', [
					'module_basename'	=> '\dmzx\chl\acp\acp_chl_module',
					'modes'	=> [
						'settings',
						'forums',
						'pages'
					],
				],
			]],
		];
	}
}
