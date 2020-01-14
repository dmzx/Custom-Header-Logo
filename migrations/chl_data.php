<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2018 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\migrations;

class chl_data extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			// Add configs
			array('config.add', array('chl_version', '1.0.0')),

			// Add upload directories
			array('custom', array(array($this, 'upload_directory_chl_logos'))),
			array('custom', array(array($this, 'upload_directory_chl_backgrounds'))),
		);
	}

	public function upload_directory_chl_logos()
	{
		$directory = 'images/chl_logos';

		if (!is_dir($this->phpbb_root_path . $directory))
		{
			@mkdir($this->phpbb_root_path . $directory, 0755);
			@chmod($this->phpbb_root_path . $directory, 0755);
		}
	}

	public function upload_directory_chl_backgrounds()
	{
		$directory = 'images/chl_backgrounds';

		if (!is_dir($this->phpbb_root_path . $directory))
		{
			@mkdir($this->phpbb_root_path . $directory, 0755);
			@chmod($this->phpbb_root_path . $directory, 0755);
		}
	}
}
