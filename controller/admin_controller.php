<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Admin controller
*/
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

	/** @var \phpbb\extension\manager */
	protected $phpbb_extension_manager;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $phpEx;

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
	* @param \phpbb\extension\manager 			$phpbb_extension_manager
	* @param string 							$phpbb_root_path
	* @param string 							$phpEx
	* @param string 							$header_images_table
	*
	*/

	public function __construct(\phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\extension\manager $phpbb_extension_manager, $phpbb_root_path, $phpEx, $header_images_table)
	{
		$this->config 				= $config;
		$this->template 			= $template;
		$this->user 				= $user;
		$this->db 					= $db;
		$this->request 				= $request;
		$this->phpbb_extension_manager 	= $phpbb_extension_manager;
		$this->phpbb_root_path 		= $phpbb_root_path;
		$this->phpEx 				= $phpEx;
		$this->header_images_table 	= $header_images_table;
	}

	/**
	* Display the options a user can configure for this extension
	*
	* @return null
	* @access public
	*/
	public function display_forums()
	{
		add_form_key('acp_header_images');

		$form_action = $this->u_action. '&amp;action=add';
		$action = $this->request->variable('action', '');
		$action = (isset($_POST['submit']) && !isset($_POST['id'])) ? 'add' : $action;
		$id = $this->request->variable('id', 0);
		$forum_id = intval(implode($this->request->variable('forum_id', array(0))));
		$backgroundimage = $this->request->variable('backgroundimage', '');
		$logoimage = $this->request->variable('logoimage', '');
		$disabled_ids = array();

		$lang_mode = $this->user->lang['CHI_TITLE_ADD'];

		//Make SQL Array
		$sql_ary = array(
			'forum_id'				=> $forum_id,
			'page_name'				=> '',
			'page_logo'				=> $logoimage,
			'page_background_logo'	=> $backgroundimage,
			'page_path'				=> '',
			'page_query'			=> '',
		);

		// Is there already an entry? Disable corresponding forum/category
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
			// Add Page
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
					$this->db->sql_query('INSERT INTO ' . $this->header_images_table	. ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
					trigger_error($this->user->lang['CHI_ADDED'] . adm_back_link($this->u_action));
				}
			break;

			// Edit Page
			case 'edit':

				$form_action = $this->u_action. '&amp;action=update';
				$lang_mode = $this->user->lang['CHI_TITLE_EDIT'];
				$sql = 'SELECT *
					FROM ' . $this->header_images_table	. '
					WHERE page_header_image_id = ' . $id;
				$result = $this->db->sql_query_limit($sql,1);
				$row = $this->db->sql_fetchrow($result);

				$this->template->assign_vars(array(
					'ID'			=> $row['page_header_image_id'],
				));

				$this->db->sql_freeresult($result);

				$page_logo_selected = $row['page_logo'];
				$page_background_selected = $row['page_background_logo'];
				$forum_id = $row['forum_id'];

				foreach($disabled_ids as $key => $value)
				{
					if($value == $forum_id) {
						unset($disabled_ids[$key]);
					}
				}

				$forums_list = make_forum_select($forum_id, $disabled_ids, true, false, false);

			break;

			// Update Page
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
					trigger_error($this->user->lang['CHI_UPDATED'] . adm_back_link($this->u_action));
				}
			break;

			// Delete Page
			case 'delete':

				if (confirm_box(true))
				{
					$sql = 'DELETE FROM ' . $this->header_images_table	. '
						WHERE page_header_image_id = ' . $id;
					$this->db->sql_query($sql);
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

		//
		// Start output the page
		//
		// List all Pages
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

		$imglist = filelist($this->phpbb_root_path . 'ext/dmzx/chl/images/logos', '');
		$logo_list = '<option value="">' . $this->user->lang['NO_LOGO'] . '</option>';

		foreach ($imglist as $path => $img_ary)
		{
			sort($img_ary);

			foreach ($img_ary as $img)
			{
				$img = $path . $img;
				$selected = ($action == 'edit' && $img == $page_logo_selected) ? ' selected="selected"' : '';
				$logo_list .= '<option value="' . htmlspecialchars($img) . '"' . $selected . '>' . htmlspecialchars($img) . '</option>';
			}
		}
		$imglist_bg = filelist($this->phpbb_root_path . 'ext/dmzx/chl/images/backgrounds', '');
		$logo_list_bg = '<option value="">' . $this->user->lang['NO_BACKGROUND_LOGO'] . '</option>';

		foreach ($imglist_bg as $path => $img_ary)
		{
			sort($img_ary);

			foreach ($img_ary as $img)
			{
				$img = $path . $img;
				$selected = ($action == 'edit' && $img == $page_background_selected) ? ' selected="selected"' : '';
				$logo_list_bg .= '<option value="' . htmlspecialchars($img) . '"' . $selected . '>' . htmlspecialchars($img) . '</option>';
			}
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

		// Version check
		$this->user->add_lang(array('install', 'acp/extensions', 'migrator'));
		$ext_name = 'dmzx/chl';
		$md_manager = new \phpbb\extension\metadata_manager($ext_name, $this->config, $this->phpbb_extension_manager, $this->template, $this->user, $this->phpbb_root_path);
		try
		{
			$this->metadata = $md_manager->get_metadata('all');
		}
		catch(\phpbb\extension\exception $e)
		{
			trigger_error($e, E_USER_WARNING);
		}
		$md_manager->output_template_data();
		try
		{
			$updates_available = $this->version_check($md_manager, $this->request->variable('versioncheck_force', false));
			$this->template->assign_vars(array(
				'S_UP_TO_DATE'		=> empty($updates_available),
				'S_VERSIONCHECK'	=> true,
				'UP_TO_DATE_MSG'	=> $this->user->lang(empty($updates_available) ? 'UP_TO_DATE' : 'NOT_UP_TO_DATE', $md_manager->get_metadata('display-name')),
			));
			foreach ($updates_available as $branch => $version_data)
			{
				$this->template->assign_block_vars('updates_available', $version_data);
			}
		}
		catch (\RuntimeException $e)
		{
			$this->template->assign_vars(array(
				'S_VERSIONCHECK_STATUS'			=> $e->getCode(),
				'VERSIONCHECK_FAIL_REASON'		=> ($e->getMessage() !== $this->user->lang('VERSIONCHECK_FAIL')) ? $e->getMessage() : '',
			));
		}
	}

	public function display_pages()
	{
		add_form_key('acp_header_images');
		$form_action = $this->u_action. '&amp;action=add';
		$action = $this->request->variable('action', '');
		$action = (isset($_POST['submit']) && !isset($_POST['id'])) ? 'add' : $action;
		$id = $this->request->variable('id', 0);
		$lang_mode = $this->user->lang['CHI_TITLE_ADD'];
		$backgroundimage = $this->request->variable('backgroundimage', '');
		$logoimage = $this->request->variable('logoimage', '');
		$pagename = $this->request->variable('pagename', '');
		$custom_page_path = $this->request->variable('custom_page_path', '');
		$custom_page_query = $this->request->variable('custom_page_query', '');

		//Make SQL Array
		$sql_ary = array(
			'page_name'				=> $pagename,
			'page_logo'				=> $logoimage,
			'page_background_logo'	=> $backgroundimage,
			'page_path'				=> $custom_page_path,
			'page_query'			=> $custom_page_query,
		);

		switch ($action)
		{
			// Add Page
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
					$this->db->sql_query('INSERT INTO ' . $this->header_images_table	.' ' . $this->db->sql_build_array('INSERT', $sql_ary));
					trigger_error($this->user->lang['CHI_ADDED'] . adm_back_link($this->u_action));
				}
			break;

			// Edit Page
			case 'edit':

				$form_action = $this->u_action. '&amp;action=update';
				$lang_mode = $this->user->lang['CHI_TITLE_EDIT'];
				$sql = 'SELECT *
					FROM ' . $this->header_images_table	. '
					WHERE page_header_image_id = ' . $id;
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

			// Update Page
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
					trigger_error($this->user->lang['CHI_UPDATED'] . adm_back_link($this->u_action));
				}
			break;

			// Delete Page
			case 'delete':

				if (confirm_box(true))
				{
					$sql = 'DELETE FROM ' . $this->header_images_table	. '
						WHERE page_header_image_id = ' . $id;
					$this->db->sql_query($sql);
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

		//
		// Start output the page
		//
		// List all Pages
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

		$imglist = filelist($this->phpbb_root_path . 'ext/dmzx/chl/images/logos', '');
		$logo_list = '<option value="">' . $this->user->lang['NO_LOGO'] . '</option>';

		foreach ($imglist as $path => $img_ary)
		{
			sort($img_ary);

			foreach ($img_ary as $img)
			{
				$img = $path . $img;
				$selected = ($action == 'edit' && $img == $page_logo_selected) ? ' selected="selected"' : '';
				$logo_list .= '<option value="' . htmlspecialchars($img) . '"' . $selected . '>' . htmlspecialchars($img) . '</option>';
			}
		}
		$imglist_bg = filelist($this->phpbb_root_path . 'ext/dmzx/chl/images/backgrounds', '');
		$logo_list_bg = '<option value="">' . $this->user->lang['NO_BACKGROUND_LOGO'] . '</option>';

		foreach ($imglist_bg as $path => $img_ary)
		{
			sort($img_ary);

			foreach ($img_ary as $img)
			{
				$img = $path . $img;
				$selected = ($action == 'edit' && $img == $page_background_selected) ? ' selected="selected"' : '';
				$logo_list_bg .= '<option value="' . htmlspecialchars($img) . '"' . $selected . '>' . htmlspecialchars($img) . '</option>';
			}
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

		// Version check
		$this->user->add_lang(array('install', 'acp/extensions', 'migrator'));
		$ext_name = 'dmzx/chl';
		$md_manager = new \phpbb\extension\metadata_manager($ext_name, $this->config, $this->phpbb_extension_manager, $this->template, $this->user, $this->phpbb_root_path);
		try
		{
			$this->metadata = $md_manager->get_metadata('all');
		}
		catch(\phpbb\extension\exception $e)
		{
			trigger_error($e, E_USER_WARNING);
		}
		$md_manager->output_template_data();
		try
		{
			$updates_available = $this->version_check($md_manager, $this->request->variable('versioncheck_force', false));
			$this->template->assign_vars(array(
				'S_UP_TO_DATE'		=> empty($updates_available),
				'S_VERSIONCHECK'	=> true,
				'UP_TO_DATE_MSG'	=> $this->user->lang(empty($updates_available) ? 'UP_TO_DATE' : 'NOT_UP_TO_DATE', $md_manager->get_metadata('display-name')),
			));
			foreach ($updates_available as $branch => $version_data)
			{
				$this->template->assign_block_vars('updates_available', $version_data);
			}
		}
		catch (\RuntimeException $e)
		{
			$this->template->assign_vars(array(
				'S_VERSIONCHECK_STATUS'			=> $e->getCode(),
				'VERSIONCHECK_FAIL_REASON'		=> ($e->getMessage() !== $this->user->lang('VERSIONCHECK_FAIL')) ? $e->getMessage() : '',
			));
		}
	}

	public function display_settings()
	{
		add_form_key('acp_header_images');
		$submit = (isset($_POST['submit_settings'])) ? true : false;
		if ($submit)
		{
			if (!check_form_key('acp_header_images'))
			{
				trigger_error($this->user->lang['FORM_INVALID']);
			}
			$this->config->set('chi_enable', $this->request->variable('chi_enable', 0));
			$this->config->set('chi_enable_guests', $this->request->variable('chi_enable_guests', 0));

			trigger_error($this->user->lang['CHI_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->template->assign_vars(array(
			'L_ACP_CHI_DESC'				=> $this->user->lang['ACP_CHI_DESC_SETTINGS'],
			'L_ACP_CHI_TITLE'				=> $this->user->lang['ACP_CHI_SETTINGS_TITLE'],
			'CHI_ENABLE'					=> $this->config['chi_enable'],
			'CHI_ENABLE_GUESTS'				=> $this->config['chi_enable_guests'],
			'S_SELECT_SETTINGS'				=> true,
		));

		// Version check
		$this->user->add_lang(array('install', 'acp/extensions', 'migrator'));
		$ext_name = 'dmzx/chl';
		$md_manager = new \phpbb\extension\metadata_manager($ext_name, $this->config, $this->phpbb_extension_manager, $this->template, $this->user, $this->phpbb_root_path);
		try
		{
			$this->metadata = $md_manager->get_metadata('all');
		}
		catch(\phpbb\extension\exception $e)
		{
			trigger_error($e, E_USER_WARNING);
		}
		$md_manager->output_template_data();
		try
		{
			$updates_available = $this->version_check($md_manager, $this->request->variable('versioncheck_force', false));
			$this->template->assign_vars(array(
				'S_UP_TO_DATE'		=> empty($updates_available),
				'S_VERSIONCHECK'	=> true,
				'UP_TO_DATE_MSG'	=> $this->user->lang(empty($updates_available) ? 'UP_TO_DATE' : 'NOT_UP_TO_DATE', $md_manager->get_metadata('display-name')),
			));
			foreach ($updates_available as $branch => $version_data)
			{
				$this->template->assign_block_vars('updates_available', $version_data);
			}
		}
		catch (\RuntimeException $e)
		{
			$this->template->assign_vars(array(
				'S_VERSIONCHECK_STATUS'			=> $e->getCode(),
				'VERSIONCHECK_FAIL_REASON'		=> ($e->getMessage() !== $this->user->lang('VERSIONCHECK_FAIL')) ? $e->getMessage() : '',
			));
		}
	}

	protected function version_check(\phpbb\extension\metadata_manager $md_manager, $force_update = false, $force_cache = false)
	{
		global $cache;
		$meta = $md_manager->get_metadata('all');
		if (!isset($meta['extra']['version-check']))
		{
			throw new \RuntimeException($this->user->lang('NO_VERSIONCHECK'), 1);
		}
		$version_check = $meta['extra']['version-check'];
		$version_helper = new \phpbb\version_helper($cache, $this->config, new \phpbb\file_downloader(), $this->user);
		$version_helper->set_current_version($meta['version']);
		$version_helper->set_file_location($version_check['host'], $version_check['directory'], $version_check['filename']);
		$version_helper->force_stability($this->config['extension_force_unstable'] ? 'unstable' : null);
		return $updates = $version_helper->get_suggested_updates($force_update, $force_cache);
	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return null
	* @access public
	*/
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}