<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\migrations;

class chl_schema extends \phpbb\db\migration\migration
{
	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'header_images'	=> array(
					'COLUMNS'	=> array(
					'page_header_image_id' 	=> array('UINT', NULL, 'auto_increment'),
					'page_name' 			=> array('VCHAR', ''),
					'forum_id' 				=> array('UINT', 0),
					'page_logo' 			=> array('VCHAR', ''),
					'page_background_logo' 	=> array('VCHAR', ''),
					'page_query' 			=> array('VCHAR', ''),
					'page_path' 			=> array('VCHAR', ''),
					),
				'PRIMARY_KEY'	=> 'page_header_image_id',
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'header_images',
			),
		);
	}
}