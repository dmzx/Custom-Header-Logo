<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2018 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\acp;

class acp_chl_module
{
	public $u_action;

	function main($id, $mode)
	{
		global $phpbb_container, $request, $user;

		$user->add_lang_ext('dmzx/chl', 'acp_chl');

		$admin_controller = $phpbb_container->get('dmzx.chl.admin.controller');

		$action = $request->variable('action', '');

		if ($request->is_set_post('add'))
		{
			$action = 'add';
		}

		$admin_controller->set_page_url($this->u_action);

		switch ($mode)
		{
			case 'forums':
				$this->tpl_name = 'acp_header_images';
				$this->page_title = $user->lang('ACP_CHI_FORUMS_TITLE');
				$admin_controller->display_forums();
			break;

			case 'pages':
				$this->tpl_name = 'acp_header_images';
				$this->page_title = $user->lang('ACP_CHI_PAGES_TITLE');
				$admin_controller->display_pages();
			break;

			case 'settings':
				$this->tpl_name = 'acp_header_images';
				$this->page_title = $user->lang('ACP_CHI_SETTINGS_TITLE');
				$admin_controller->display_settings();
			break;
		}
	}
}
