<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2018 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\migrations;

class chl_schema extends \phpbb\db\migration\migration
{
	public function update_schema()
	{
		return [
			'add_tables'	=> [
				$this->table_prefix . 'header_images'	=> [
					'COLUMNS'	=> [
						'page_header_image_id' 	=> ['UINT', null, 'auto_increment'],
						'page_name' 			=> ['VCHAR', ''],
						'forum_id' 				=> ['UINT', 0],
						'page_logo' 			=> ['VCHAR', ''],
						'page_background_logo' 	=> ['VCHAR', ''],
						'page_query' 			=> ['VCHAR', ''],
						'page_path' 			=> ['VCHAR', ''],
					],
					'PRIMARY_KEY'	=> 'page_header_image_id',
				],
			],
		];
	}

	public function revert_schema()
	{
		return [
			'drop_tables' => [
				$this->table_prefix . 'header_images',
			],
		];
	}
}
