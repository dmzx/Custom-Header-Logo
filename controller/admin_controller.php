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

	/**
	* Constructor
	*
	* @param \phpbb\config\config				$config
	* @param \phpbb\template\template		 	$template
	* @param \phpbb\user						$user
	* @param \phpbb\db\driver\driver_interface	$db
	* @param \phpbb\request\request		 		$request
	* @param \phpbb\log\log_interface			$log
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

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_SETTINGS_UPDATED', false, [$this->user->data['username']]);

			trigger_error($this->user->lang['CHI_SETTINGS_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->template->assign_vars([
			'L_ACP_CHI_DESC'			=> $this->user->lang['ACP_CHI_DESC_SETTINGS'],
			'L_ACP_CHI_TITLE'			=> $this->user->lang['ACP_CHI_SETTINGS_TITLE'],
			'CHI_ENABLE'				=> $this->config['chi_enable'],
			'CHI_ENABLE_GUESTS'			=> $this->config['chi_enable_guests'],
			'CHI_WIDTH_SET'				=> $this->config['chi_width_set'],
			'CHI_HEIGHT_SET'			=> $this->config['chi_height_set'],
			'ACP_CHI_VERSION'			=> $this->config['chl_version'],
			'CHI_SHOWPAGENAME'			=> $this->config['chi_showpagename'],
			'S_SELECT_SETTINGS'			=> true,
		]);
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
		$disabled_ids = [];

		$lang_mode = $this->user->lang['CHI_TITLE_ADD'];

		$sql_ary = [
			'forum_id'				=> $forum_id,
			'page_name'				=> '',
			'page_logo'				=> $logoimage,
			'page_background_logo'	=> $backgroundimage,
			'page_path'				=> '',
			'page_query'			=> '',
		];

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
					trigger_error($this->user->lang['CHI_NEED_FORUM'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				else
				{
					$this->db->sql_query('INSERT INTO ' . $this->header_images_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_ADDED', false, [$this->user->data['username']]);

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

				foreach ($disabled_ids as $key => $value)
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
					trigger_error($this->user->lang['CHI_NEED_PAGE'] . adm_back_link($this->u_action . '&amp;action=edit&amp;id=' . $id), E_USER_WARNING);
				}
				else
				{
					$this->db->sql_query('UPDATE ' . $this->header_images_table	. ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE page_header_image_id = ' . $id);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_UPDATED', false, [$this->user->data['username']]);

					trigger_error($this->user->lang['CHI_UPDATED'] . adm_back_link($this->u_action));
				}
			break;

			case 'delete':

				if (confirm_box(true))
				{
					$sql = 'DELETE FROM ' . $this->header_images_table	. '
						WHERE page_header_image_id = ' . (int) $id;
					$this->db->sql_query($sql);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_DELETED', false, [$this->user->data['username']]);

					trigger_error($this->user->lang['CHI_DELETED'] . adm_back_link($this->u_action));
				}
				else
				{
					confirm_box(false, $this->user->lang['CHI_REALY_DELETE'], build_hidden_fields([
						'id'			=> $id,
						'action'	=> 'delete',
					]));
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
			$this->template->assign_block_vars('customheaderimages_forum', [
				'FORUMNAME'		=> $row['forum_name'],
				'LOGO'			=> $row['page_logo'],
				'BACKGROUND'	=> $row['page_background_logo'],
				'U_EDIT'		=> $this->u_action . '&amp;action=edit&amp;id=' .$row['page_header_image_id'],
				'U_DEL'			=> $this->u_action . '&amp;action=delete&amp;id=' .$row['page_header_image_id'],
			]);
		}
		$this->db->sql_freeresult($result);

		$imglist = filelist($this->root_path . 'images/chl_logos', '');
		$logo_list = '<option value="">' . $this->user->lang['NO_LOGO'] . '</option>';

		foreach ($imglist as $path => $img_ary)
		{
			sort($img_ary);

			foreach ($img_ary as $img)
			{
				$img = $path . $img;
				$selected = ($action == 'edit' && $img == $page_logo_selected) ? ' selected="selected"' : '';
				$logo_list .= '<option value="' . utf8_htmlspecialchars($img) . '"' . $selected . '>' . utf8_htmlspecialchars($img) . '</option>';
			}
		}
		$imglist_bg = filelist($this->root_path . 'images/chl_backgrounds', '');
		$logo_list_bg = '<option value="">' . $this->user->lang['NO_BACKGROUND_LOGO'] . '</option>';

		foreach ($imglist_bg as $path => $img_ary)
		{
			sort($img_ary);

			foreach ($img_ary as $img)
			{
				$img = $path . $img;
				$selected = ($action == 'edit' && $img == $page_background_selected) ? ' selected="selected"' : '';
				$logo_list_bg .= '<option value="' . utf8_htmlspecialchars($img) . '"' . $selected . '>' . utf8_htmlspecialchars($img) . '</option>';
			}
		}

		$this->template->assign_vars([
			'U_ACTION'			=> $form_action,
			'L_MODE_TITLE'		=> $lang_mode,
			'L_ACP_CHI_DESC'	=> $this->user->lang['ACP_CHI_DESC_FORUMS'],
			'L_ACP_CHI_TITLE'	=> $this->user->lang['ACP_CHI_FORUMS_TITLE'],
			'S_LOGO_LIST'		=> $logo_list,
			'S_BACKGROUND_LIST' => $logo_list_bg,
			'S_SELECT_FORUMS'	=> true,
			'S_FORUM_OPTIONS'	=> $forums_list,
		]);
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

		$sql_ary = [
			'page_name'				=> $pagename,
			'page_logo'				=> $logoimage,
			'page_background_logo'	=> $backgroundimage,
			'page_path'				=> $custom_page_path,
			'page_query'			=> $custom_page_query,
		];

		switch ($action)
		{
			case 'add':

				if (!check_form_key('acp_header_images'))
				{
					trigger_error($this->user->lang['FORM_INVALID']);
				}

				if ($pagename == 'viewforum')
				{
					trigger_error($this->user->lang['CHI_FORUM_FORBIDDEN'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				if ($pagename == '' || ($logoimage == '' && $backgroundimage == ''))
				{
					trigger_error($this->user->lang['CHI_NEED_PAGE'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				else
				{
					$this->db->sql_query('INSERT INTO ' . $this->header_images_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_ADDED', false, [$this->user->data['username']]);

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

				$this->template->assign_vars([
					'ID'			=> $row['page_header_image_id'],
					'PAGENAME'		=> $row['page_name'],
					'PATH'			=> $row['page_path'],
					'QUERY'			=> $row['page_query'],
				]);
			break;

			case 'update':

				if (!check_form_key('acp_header_images'))
				{
					trigger_error($this->user->lang['FORM_INVALID']);
				}

				if ($pagename == '' || ($logoimage == '' && $backgroundimage == ''))
				{
					trigger_error($this->user->lang['CHI_NEED_PAGE'] . adm_back_link($this->u_action . '&amp;action=edit&amp;id=' . $id), E_USER_WARNING);
				}
				else
				{
					$this->db->sql_query('UPDATE ' . $this->header_images_table	. ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE page_header_image_id = ' . $id);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_UPDATED', false, [$this->user->data['username']]);

					trigger_error($this->user->lang['CHI_UPDATED'] . adm_back_link($this->u_action));
				}
			break;

			case 'delete':

				if (confirm_box(true))
				{
					$sql = 'DELETE FROM ' . $this->header_images_table	. '
						WHERE page_header_image_id = ' . (int) $id;
					$this->db->sql_query($sql);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CHI_DELETED', false, [$this->user->data['username']]);

					trigger_error($this->user->lang['CHI_DELETED'] . adm_back_link($this->u_action));
				}
				else
				{
					confirm_box(false, $this->user->lang['CHI_REALY_DELETE'], build_hidden_fields([
						'id'		=> $id,
						'action'	=> 'delete',
					]));
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
			$this->template->assign_block_vars('customheaderimages', [
				'PAGENAME'		=> $row['page_name'],
				'LOGO'			=> $row['page_logo'],
				'PATH'			=> $row['page_path'],
				'QUERY'			=> $row['page_query'],
				'BACKGROUND'	=> $row['page_background_logo'],
				'U_EDIT'		=> $this->u_action . '&amp;action=edit&amp;id=' .$row['page_header_image_id'],
				'U_DEL'			=> $this->u_action . '&amp;action=delete&amp;id=' .$row['page_header_image_id'],
			]);
		}
		$this->db->sql_freeresult($result);

		$imglist = filelist($this->root_path . 'images/chl_logos', '');
		$logo_list = '<option value="">' . $this->user->lang['NO_LOGO'] . '</option>';

		foreach ($imglist as $path => $img_ary)
		{
			sort($img_ary);

			foreach ($img_ary as $img)
			{
				$img = $path . $img;
				$selected = ($action == 'edit' && $img == $page_logo_selected) ? ' selected="selected"' : '';
				$logo_list .= '<option value="' . utf8_htmlspecialchars($img) . '"' . $selected . '>' . utf8_htmlspecialchars($img) . '</option>';
			}
		}
		$imglist_bg = filelist($this->root_path . 'images/chl_backgrounds', '');
		$logo_list_bg = '<option value="">' . $this->user->lang['NO_BACKGROUND_LOGO'] . '</option>';

		foreach ($imglist_bg as $path => $img_ary)
		{
			sort($img_ary);

			foreach ($img_ary as $img)
			{
				$img = $path . $img;
				$selected = ($action == 'edit' && $img == $page_background_selected) ? ' selected="selected"' : '';
				$logo_list_bg .= '<option value="' . utf8_htmlspecialchars($img) . '"' . $selected . '>' . utf8_htmlspecialchars($img) . '</option>';
			}
		}

		$this->template->assign_vars([
			'U_ACTION'				=> $form_action,
			'L_MODE_TITLE'			=> $lang_mode,
			'L_ACP_CHI_DESC'		=> $this->user->lang['ACP_CHI_DESC_PAGES'],
			'L_ACP_CHI_TITLE'		=> $this->user->lang['ACP_CHI_PAGES_TITLE'],
			'S_LOGO_LIST'			=> $logo_list,
			'S_BACKGROUND_LIST' 	=> $logo_list_bg,
			'S_SELECT_CUSTOMPAGE'	=> true,
		]);
	}

	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
