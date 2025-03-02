<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2025 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\migrations;

class chl_v104 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return [
			'\dmzx\chl\migrations\chl_v103',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['chl_version', '1.0.4']],
			['config.add', ['chi_disable_site_info', 0]],
			['config.add', ['chi_allowed_extension_images', $this->allowed_image_extensions()]],
			['config.add', ['chi_allowed_extension_video', $this->allowed_video_extensions()]],
		];
	}

	private function allowed_image_extensions()
	{
		$allowed_image_extensions = [
			'gif', 'jpg', 'jpeg', 'png'
		];

		$allowed_image_extensions = implode(",", $allowed_image_extensions);

		return $allowed_image_extensions;
	}

	private function allowed_video_extensions()
	{
		$allowed_video_extensions = [
			'mp4', 'avi'
		];

		$allowed_video_extensions = implode(",", $allowed_video_extensions);

		return $allowed_video_extensions;
	}
}
