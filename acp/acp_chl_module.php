<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
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

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('dmzx.chl.admin.controller');

		// Requests
		$action = $request->variable('action', '');
		if ($request->is_set_post('add'))
		{
			$action = 'add';
		}

		// Make the $u_action url available in the admin controller
		$admin_controller->set_page_url($this->u_action);

		// Load the "settings" or "manage" module modes
		switch ($mode)
		{
			case 'forums':
				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_header_images';

				// Set the page title for our ACP page
				$this->page_title = $user->lang['ACP_CHI_FORUMS_TITLE'];

				$admin_controller->display_forums();
			break;

			case 'pages':
				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_header_images';

				// Set the page title for our ACP page
				$this->page_title = $user->lang['ACP_CHI_PAGES_TITLE'];

				$admin_controller->display_pages();
			break;

			case 'settings':
				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_header_images';

				// Set the page title for our ACP page
				$this->page_title = $user->lang['ACP_CHI_SETTINGS_TITLE'];

				// Load the display options handle in the admin controller
				$admin_controller->display_settings();
			break;
		}
	}
}

