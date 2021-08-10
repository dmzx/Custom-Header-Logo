<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2018 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\migrations;

class chl_v102 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return [
			'\dmzx\chl\migrations\chl_v101',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['chl_version', '1.0.2']],
			['config.add', ['chi_width_set', 650]],
			['config.add', ['chi_height_set', 52]],
		];
	}
}
