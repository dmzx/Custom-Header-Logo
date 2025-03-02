<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2018 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\controller;

class admin_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\log\log_interface */
	protected $log;

	/** @var \phpbb\path_helper */
	protected $path_helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/**
	* The database table
	*
	* @var string
	*/
	protected $header_images_table;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param \phpbb\config\config				$config
	* @param \phpbb\template\template		 	$template
	* @param \phpbb\user						$user
	* @param \phpbb\db\driver\driver_interface	$db
	* @param \phpbb\request\request		 		$request
	* @param \phpbb\log\log_interface			$log
	* @param \phpbb\path_helper 				$path_helper
	* @param string 							$root_path
	* @param string 							$php_ext
	* @param string 							$header_images_table
	*
	*/
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\request\request $request,
		\phpbb\log\log_interface $log,
		\phpbb\path_helper $path_helper,
		$root_path,
		$php_ext,
		$header_images_table
	)
	{
		$this->config 				= $config;
		$this->template 			= $template;
		$this->user 				= $user;
		$this->db 					= $db;
		$this->request 				= $request;
		$this->log					= $log;
		$this->path_helper 			= $path_helper;
		$this->root_path 			= $root_path;
		$this->php_ext 				= $php_ext;
		$this->header_images_table 	= $header_images_table;
	}

	public function display_settings()
	{
		add_form_key('acp_header_images');

		if ($this->request->is_set_post('submit_settings'))
		{
			if (!check_form_key('acp_header_images'))
			{
				trigger_error($this->user->lang['FORM_INVALID']);
			}

			$this->config->set('chi_enable', $this->request->variable('chi_enable', 0));
			$this->config->set('chi_enable_guests', $this->request->variable('chi_enable_guests', 0));
			$this->config->set('chi_width_set', $this->request->variable('chi_width_set', 0));
			$this->config->set('chi_height_set', $this->request->variable('chi_height_set', 0));
			$this->config->set('chi_showpagename', $this->request->variable('chi_showpagename', 0));
			$this->config->set('chi_disable_site_info', $this->request->variable('chi_disable_site_info', 0));

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_SETTINGS_UPDATED', false, array($this->user->data['username']));

			meta_refresh(1, append_sid($this->u_action));
			trigger_error($this->user->lang['CHI_SETTINGS_UPDATED'] . adm_back_link($this->u_action));
		}

		if ($this->request->is_set_post('acp_header_images_upload'))
		{
			if (!check_form_key('acp_header_images'))
			{
				trigger_error($this->user->lang['FORM_INVALID']);
			}

			$this->config->set('chi_allowed_extension_images', $this->request->variable('chi_allowed_extension_images', ''));
			$this->config->set('chi_allowed_extension_video', $this->request->variable('chi_allowed_extension_video', ''));

			$directory_chl_logos = 'images' . '/' . 'chl_logos' . '/';
			$directory_chl_backgrounds = 'images' . '/' . 'chl_backgrounds' . '/';

			// Upload logo, header image and favicon icon
			foreach (array("chi_bg_upload", "chi_logos_upload") as $file_key)
			{
				$file = $this->request->file($file_key);

				if (isset($file["name"]) && $file["name"] && $file["error"] == UPLOAD_ERR_OK)
				{
					// Get allowed extensions
					$allowed_extension = $this->get_allowed_extensions($file_key);

					$board_url = generate_board_url() . '/';
					$corrected_path = $this->path_helper->get_web_root_path();

					// Determine the image path based on file key
					$image_path = ((defined('PHPBB_USE_BOARD_URL_PATH') && PHPBB_USE_BOARD_URL_PATH) ? $board_url : $corrected_path) . ($file_key == "chi_bg_upload" ? $directory_chl_backgrounds : $directory_chl_logos);

					// Get the file extension
					$file_extension = pathinfo($file["name"], PATHINFO_EXTENSION);

					// Maximum size allowed
					$max_size_allowed = ((int) ini_get("upload_max_filesize")) * 1000000;

					// Check if image extension is allowed
					if (!in_array($file_extension, $allowed_extension, true))
					{
						meta_refresh(1, append_sid($this->u_action));
						trigger_error($this->user->lang('ACP_CHI_IMAGE_EXT_NOT_ALLOWED', $file["name"]) . adm_back_link($this->u_action), E_USER_WARNING);
					}
					// Check if image size is allowed
					elseif ($file["size"] > $max_size_allowed)
					{
						meta_refresh(1, append_sid($this->u_action));
						trigger_error($this->user->lang('ACP_CHI_IMAGE_FILE_SIZE', $file["name"]) . adm_back_link($this->u_action), E_USER_WARNING);
					}
					else
					{
						// Upload the new one
						$destination = $image_path;

						if ($this->upload($file, $destination))
						{
							meta_refresh(3, append_sid($this->u_action));
							trigger_error($this->user->lang('ACP_CHI_IMAGE_FILE_UPLOADED', $file["name"]) . adm_back_link($this->u_action));
						}
						else
						{
							meta_refresh(1, append_sid($this->u_action));
							trigger_error($this->user->lang('ACP_CHI_IMAGE_FILE_FAIL', $file["name"]) . adm_back_link($this->u_action), E_USER_WARNING);
						}
					}
				}
			}
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_SETTINGS_UPDATED', false, array($this->user->data['username']));

			meta_refresh(1, append_sid($this->u_action));
			trigger_error($this->user->lang['CHI_SETTINGS_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->template->assign_vars(array(
			'L_ACP_CHI_DESC'				=> $this->user->lang['ACP_CHI_DESC_SETTINGS'],
			'L_ACP_CHI_TITLE'				=> $this->user->lang['ACP_CHI_SETTINGS_TITLE'],
			'CHI_ENABLE'					=> $this->config['chi_enable'],
			'CHI_ENABLE_GUESTS'				=> $this->config['chi_enable_guests'],
			'CHI_WIDTH_SET'					=> $this->config['chi_width_set'],
			'CHI_HEIGHT_SET'				=> $this->config['chi_height_set'],
			'ACP_CHI_VERSION'				=> $this->config['chl_version'],
			'CHI_SHOWPAGENAME'				=> $this->config['chi_showpagename'],
			'CHI_DISABLE_SITE_INFO'			=> $this->config['chi_disable_site_info'],
			'CHI_ALLOWED_EXTENSION_IMAGES'	=> $this->config['chi_allowed_extension_images'],
			'CHI_ALLOWED_EXTENSION_VIDEO'	=> $this->config['chi_allowed_extension_video'],
			'CHI_ALLOWED_EXTENSION' 		=> $this->user->lang('ACP_CHI_ALLOWED_EXTENSION', implode(', ', $this->get_allowed_extensions('chi_bg_upload'))),
			'CHI_ALLOWED_EXTENSION_IMAGE' 	=> $this->user->lang('ACP_CHI_ALLOWED_EXTENSION', implode(', ', $this->get_allowed_extensions('chi_logos_upload'))),
			'S_SELECT_SETTINGS'				=> true,
		));
	}

	public function display_forums()
	{
		add_form_key('acp_header_images');

		$form_action = $this->u_action. '&amp;action=add';
		$action = $this->request->variable('action', '');
		$action = ($this->request->is_set('submit') && !$this->request->is_set('id')) ? 'add' : $action;
		$id = $this->request->variable('id', 0);
		$forum_id = intval($this->request->variable('forum_id', 0));
		$backgroundimage = $this->request->variable('backgroundimage', '');
		$logoimage = $this->request->variable('logoimage', '');
		$disabled_ids = array();

		$lang_mode = $this->user->lang['CHI_TITLE_ADD'];

		$sql_ary = array(
			'forum_id'				=> $forum_id,
			'page_name'				=> '',
			'page_logo'				=> $logoimage,
			'page_background_logo'	=> $backgroundimage,
			'page_path'				=> '',
			'page_query'			=> '',
		);

		$sql = 'SELECT forum_id
			FROM ' . $this->header_images_table	. '
			WHERE forum_id <> 0';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$disabled_ids[] = $row['forum_id'];
		}
		$this->db->sql_freeresult($result);

		$forums_list = make_forum_select(false, $disabled_ids, true, false, false);

		switch ($action)
		{
			case 'add':

				if (!check_form_key('acp_header_images'))
				{
					trigger_error($this->user->lang['FORM_INVALID']);
				}

				if (empty($forum_id) || ($logoimage == '' && $backgroundimage == '' ))
				{
					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['CHI_NEED_FORUM'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				else
				{
					$this->db->sql_query('INSERT INTO ' . $this->header_images_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_ADDED', false, array($this->user->data['username']));

					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['CHI_ADDED'] . adm_back_link($this->u_action));
				}
			break;

			case 'edit':

				$form_action = $this->u_action. '&amp;action=update';
				$lang_mode = $this->user->lang['CHI_TITLE_EDIT'];
				$sql = 'SELECT *
					FROM ' . $this->header_images_table	. '
					WHERE page_header_image_id = ' . (int) $id;
				$result = $this->db->sql_query_limit($sql,1);
				$row = $this->db->sql_fetchrow($result);

				$this->template->assign_var('ID', $row['page_header_image_id']);

				$this->db->sql_freeresult($result);

				$page_logo_selected = $row['page_logo'];
				$page_background_selected = $row['page_background_logo'];
				$forum_id = $row['forum_id'];

				foreach($disabled_ids as $key => $value)
				{
					if ($value == $forum_id)
					{
						unset($disabled_ids[$key]);
					}
				}

				$forums_list = make_forum_select($forum_id, $disabled_ids, true, false, false);

			break;

			case 'update':
				if (!check_form_key('acp_header_images'))
				{
					trigger_error($this->user->lang['FORM_INVALID']);
				}

				if (empty($forum_id) || ($logoimage == '' && $backgroundimage == '' ))
				{
					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['CHI_NEED_PAGE'] . adm_back_link($this->u_action . '&amp;action=edit&amp;id=' . $id), E_USER_WARNING);
				}
				else
				{
					$this->db->sql_query('UPDATE ' . $this->header_images_table	. ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE page_header_image_id = ' . $id);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_UPDATED', false, array($this->user->data['username']));

					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['CHI_UPDATED'] . adm_back_link($this->u_action));
				}
			break;

			case 'delete':

				if (confirm_box(true))
				{
					$sql = 'DELETE FROM ' . $this->header_images_table	. '
						WHERE page_header_image_id = ' . (int) $id;
					$this->db->sql_query($sql);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_DELETED', false, array($this->user->data['username']));

					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['CHI_DELETED'] . adm_back_link($this->u_action));
				}
				else
				{
					confirm_box(false, $this->user->lang['CHI_REALY_DELETE'], build_hidden_fields(array(
						'id'			=> $id,
						'action'	=> 'delete',
					)));
				}
			break;
		}

		$sql = 'SELECT hi.page_header_image_id, f.forum_name, hi.page_logo, hi.page_background_logo
			FROM ' . $this->header_images_table	. ' hi
			LEFT JOIN ' . FORUMS_TABLE . ' f
			ON hi.forum_id = f.forum_id
			WHERE hi.forum_id <> 0
			ORDER BY left_id ASC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('customheaderimages_forum', array(
				'FORUMNAME'		=> $row['forum_name'],
				'LOGO'			=> $row['page_logo'],
				'BACKGROUND'	=> $row['page_background_logo'],
				'U_EDIT'		=> $this->u_action . '&amp;action=edit&amp;id=' .$row['page_header_image_id'],
				'U_DEL'			=> $this->u_action . '&amp;action=delete&amp;id=' .$row['page_header_image_id'],
			));
		}
		$this->db->sql_freeresult($result);

		$directory_chl_logos = $this->root_path . 'images/chl_logos';
		$imglist = [];

		$files = scandir($directory_chl_logos);

		foreach ($files as $file)
		{
			if ($file !== '.' && $file !== '..')
			{
				$imglist[] = $file;
			}
		}

		$logo_list = '<option value="">' . $this->user->lang['NO_LOGO'] . '</option>';

		sort($imglist);

		foreach ($imglist as $img)
		{
			$selected = ($action == 'edit' && $img == $page_logo_selected) ? ' selected="selected"' : '';
			$logo_list .= '<option value="' . utf8_htmlspecialchars($img) . '"' . $selected . '>' . utf8_htmlspecialchars($img) . '</option>';
		}

		$directory_chl_bg = $this->root_path . 'images/chl_backgrounds';
		$imglist_bg = [];

		$files_bg = scandir($directory_chl_bg);

		foreach ($files_bg as $file)
		{
			if ($file !== '.' && $file !== '..')
			{
				$imglist_bg[] = $file;
			}
		}

		$logo_list_bg = '<option value="">' . $this->user->lang['NO_BACKGROUND_LOGO'] . '</option>';

		sort($imglist_bg);

		foreach ($imglist_bg as $img)
		{
			$selected = ($action == 'edit' && $img == $page_background_selected) ? ' selected="selected"' : '';
			$logo_list_bg .= '<option value="' . utf8_htmlspecialchars($img) . '"' . $selected . '>' . utf8_htmlspecialchars($img) . '</option>';
		}

		$this->template->assign_vars(array(
			'U_ACTION'			=> $form_action,
			'L_MODE_TITLE'		=> $lang_mode,
			'L_ACP_CHI_DESC'	=> $this->user->lang['ACP_CHI_DESC_FORUMS'],
			'L_ACP_CHI_TITLE'	=> $this->user->lang['ACP_CHI_FORUMS_TITLE'],
			'S_LOGO_LIST'		=> $logo_list,
			'S_BACKGROUND_LIST' => $logo_list_bg,
			'S_SELECT_FORUMS'	=> true,
			'S_FORUM_OPTIONS'	=> $forums_list,
		));
	}

	public function display_pages()
	{
		add_form_key('acp_header_images');
		$form_action = $this->u_action. '&amp;action=add';
		$action = $this->request->variable('action', '');
		$action = ($this->request->is_set('submit') && !$this->request->is_set('id')) ? 'add' : $action;
		$id = $this->request->variable('id', 0);
		$lang_mode = $this->user->lang['CHI_TITLE_ADD'];
		$backgroundimage = $this->request->variable('backgroundimage', '');
		$logoimage = $this->request->variable('logoimage', '');
		$pagename = $this->request->variable('pagename', '');
		$custom_page_path = $this->request->variable('custom_page_path', '');
		$custom_page_query = $this->request->variable('custom_page_query', '');

		$sql_ary = array(
			'page_name'				=> $pagename,
			'page_logo'				=> $logoimage,
			'page_background_logo'	=> $backgroundimage,
			'page_path'				=> $custom_page_path,
			'page_query'			=> $custom_page_query,
		);

		switch ($action)
		{
			case 'add':

				if (!check_form_key('acp_header_images'))
				{
					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['FORM_INVALID']);
				}

				if ($pagename == 'viewforum')
				{
					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['CHI_FORUM_FORBIDDEN'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				if ($pagename == '' || ($logoimage == '' && $backgroundimage == ''))
				{
					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['CHI_NEED_PAGE'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				else
				{
					$this->db->sql_query('INSERT INTO ' . $this->header_images_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_ADDED', false, array($this->user->data['username']));

					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['CHI_ADDED'] . adm_back_link($this->u_action));
				}
			break;

			case 'edit':

				$form_action = $this->u_action. '&amp;action=update';
				$lang_mode = $this->user->lang['CHI_TITLE_EDIT'];
				$sql = 'SELECT *
					FROM ' . $this->header_images_table	. '
					WHERE page_header_image_id = ' . (int) $id;
				$result = $this->db->sql_query_limit($sql,1);
				$row = $this->db->sql_fetchrow($result);

				$page_logo_selected = $row['page_logo'];
				$page_background_selected = $row['page_background_logo'];

				$this->template->assign_vars(array(
					'ID'			=> $row['page_header_image_id'],
					'PAGENAME'		=> $row['page_name'],
					'PATH'			=> $row['page_path'],
					'QUERY'			=> $row['page_query'],
				));
			break;

			case 'update':

				if (!check_form_key('acp_header_images'))
				{
					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['FORM_INVALID']);
				}

				if ($pagename == '' || ($logoimage == '' && $backgroundimage == ''))
				{
					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['CHI_NEED_PAGE'] . adm_back_link($this->u_action . '&amp;action=edit&amp;id=' . $id), E_USER_WARNING);
				}
				else
				{
					$this->db->sql_query('UPDATE ' . $this->header_images_table	. ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE page_header_image_id = ' . $id);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_UPDATED', false, array($this->user->data['username']));

					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['CHI_UPDATED'] . adm_back_link($this->u_action));
				}
			break;

			case 'delete':

				if (confirm_box(true))
				{
					$sql = 'DELETE FROM ' . $this->header_images_table	. '
						WHERE page_header_image_id = ' . (int) $id;
					$this->db->sql_query($sql);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_DELETED', false, array($this->user->data['username']));

					meta_refresh(1, append_sid($this->u_action));
					trigger_error($this->user->lang['CHI_DELETED'] . adm_back_link($this->u_action));
				}
				else
				{
					confirm_box(false, $this->user->lang['CHI_REALY_DELETE'], build_hidden_fields(array(
						'id'		=> $id,
						'action'	=> 'delete',
					)));
				}
			break;
		}

		$sql = 'SELECT *
			FROM ' . $this->header_images_table	. '
			WHERE forum_id = 0
			ORDER by page_header_image_id';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('customheaderimages', array(
				'PAGENAME'		=> $row['page_name'],
				'LOGO'			=> $row['page_logo'],
				'PATH'			=> $row['page_path'],
				'QUERY'			=> $row['page_query'],
				'BACKGROUND'	=> $row['page_background_logo'],
				'U_EDIT'		=> $this->u_action . '&amp;action=edit&amp;id=' .$row['page_header_image_id'],
				'U_DEL'			=> $this->u_action . '&amp;action=delete&amp;id=' .$row['page_header_image_id'],
			));
		}
		$this->db->sql_freeresult($result);

		$directory_chl_logos = $this->root_path . 'images/chl_logos';
		$imglist = [];

		$files = scandir($directory_chl_logos);

		foreach ($files as $file)
		{
			if ($file !== '.' && $file !== '..')
			{
				$imglist[] = $file;
			}
		}

		$logo_list = '<option value="">' . $this->user->lang['NO_LOGO'] . '</option>';

		sort($imglist);

		foreach ($imglist as $img)
		{
			$selected = ($action == 'edit' && $img == $page_logo_selected) ? ' selected="selected"' : '';
			$logo_list .= '<option value="' . utf8_htmlspecialchars($img) . '"' . $selected . '>' . utf8_htmlspecialchars($img) . '</option>';
		}

		$directory_chl_bg = $this->root_path . 'images/chl_backgrounds';
		$imglist_bg = [];

		$files_bg = scandir($directory_chl_bg);

		foreach ($files_bg as $file)
		{
			if ($file !== '.' && $file !== '..')
			{
				$imglist_bg[] = $file;
			}
		}

		$logo_list_bg = '<option value="">' . $this->user->lang['NO_BACKGROUND_LOGO'] . '</option>';

		sort($imglist_bg);

		foreach ($imglist_bg as $img)
		{
			$selected = ($action == 'edit' && $img == $page_background_selected) ? ' selected="selected"' : '';
			$logo_list_bg .= '<option value="' . utf8_htmlspecialchars($img) . '"' . $selected . '>' . utf8_htmlspecialchars($img) . '</option>';
		}

		$this->template->assign_vars(array(
			'U_ACTION'				=> $form_action,
			'L_MODE_TITLE'			=> $lang_mode,
			'L_ACP_CHI_DESC'		=> $this->user->lang['ACP_CHI_DESC_PAGES'],
			'L_ACP_CHI_TITLE'		=> $this->user->lang['ACP_CHI_PAGES_TITLE'],
			'S_LOGO_LIST'			=> $logo_list,
			'S_BACKGROUND_LIST' 	=> $logo_list_bg,
			'S_SELECT_CUSTOMPAGE'	=> true,
		));
	}

	protected function upload($file, $path)
	{
		$location = $path . basename($file["name"]);

		if (@move_uploaded_file($file["tmp_name"], $location))
		{
			return true;
		}
		return false;
	}

	function get_allowed_extensions($file_key)
	{
		$allowed_extension_images = explode(',', $this->config['chi_allowed_extension_images']);
		$allowed_extension_video = explode(',', $this->config['chi_allowed_extension_video']);

		$allowed_extension = array_map('trim', $allowed_extension_images);

		// Allow upload video as header background
		if ($file_key == "chi_bg_upload")
		{
			$allowed_extension = array_merge($allowed_extension, array_map('trim', $allowed_extension_video));
		}

		return $allowed_extension;
	}

	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
