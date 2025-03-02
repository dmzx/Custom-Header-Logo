<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2018 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
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
		$php_ext,
		$header_images_table
	)
	{
		$this->config 				= $config;
		$this->template 			= $template;
		$this->user 				= $user;
		$this->db 					= $db;
		$this->request 				= $request;
		$this->php_ext 				= $php_ext;
		$this->header_images_table 	= $header_images_table;
	}

	static public function getSubscribedEvents()
	{
		return [
			'core.page_header_after'	=> 'page_header_after',
		];
	}

	public function page_header_after($event)
	{
		$site_logo = $this->user->img('site_logo');
		$site_background_logo = '';

		if (isset($this->config['chi_enable']))
		{
			if ($this->config['chi_enable'] && ($this->config['chi_enable_guests'] && empty($this->user->data['is_registered']) || !empty($this->user->data['is_registered'])))
			{
				$post = $this->request->is_set('f');
				// Are we in a forum, topic or a posting page with logo/bg logo?
				if ((($this->user->page['page_name'] == "viewforum.$this->php_ext") || ($this->user->page['page_name'] == "viewtopic.$this->php_ext") || ($this->user->page['page_name'] == "posting.$this->php_ext")) && !empty($post) && !is_array($post))
				{
					$forum_id = $this->request->variable('f', 0);
					$sql = 'SELECT page_logo, page_background_logo
						FROM ' . $this->header_images_table . '
						WHERE forum_id = ' . (int) $forum_id;
					$result = $this->db->sql_query($sql, 60);
					$forum_logo = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);
					$site_logo_custom = $forum_logo['page_logo'] ?? '';
					$header_background_custom = $forum_logo['page_background_logo'] ?? '';
				}

				// Are we on a page where we defined a logo and/or header background image?
				if ($this->user->page['page_name'] != "viewforum.$this->php_ext")
				{
					$sql_where = "page_name = '" . $this->db->sql_escape(str_replace('.' . $this->php_ext, '', $this->user->page['page_name'])) . "'
						AND page_path = '" . $this->db->sql_escape($this->user->page['page_dir']) . "'
						AND forum_id = 0";

					$sql = 'SELECT page_logo, page_background_logo, page_query, page_path
						FROM ' . $this->header_images_table . "
						WHERE $sql_where
						ORDER BY page_query DESC, page_path DESC";
					$result = $this->db->sql_query($sql, 60);

					while ($row = $this->db->sql_fetchrow($result))
					{
						if (stripos($this->user->page['query_string'],$row['page_query']) !== false || $row['page_query'] == '*' || $row['page_query'] == $this->user->page['query_string'])
						{
							$site_logo_custom = $row['page_logo'];
							$header_background_custom = $row['page_background_logo'];

							if ($this->config['chi_disable_site_info'] == true)
							{
								$this->template->assign_var('SITENAME', '');
								$this->template->assign_var('SITE_DESCRIPTION', '');
							}
							break;
						}
					}
					$this->db->sql_freeresult($result);
				}

				$this->board_url = generate_board_url() . '/';

				// Create the link(s)
				if (!empty($site_logo_custom))
				{
					$site_logo = $this->board_url . 'images/chl_logos/' . $site_logo_custom;

					$this->template->assign_var('SITE_LOGO_IMG', $site_logo);
				}
				else
				{
					if (phpbb_version_compare($this->config['version'], '3.3.0', '<'))
					{
						$site_logo = $this->board_url . 'styles/prosilver/theme/images/site_logo.gif';

						$this->template->assign_var('SITE_LOGO_IMG', false);
					}
					else
					{
						$site_logo = $this->board_url . 'styles/prosilver/theme/images/site_logo.svg';

						$this->template->assign_var('SITE_LOGO_IMG', false);
					}
				}

				if (!empty($header_background_custom))
				{
					$site_background_logo = $this->board_url . 'images/chl_backgrounds/' . $header_background_custom;

					$this->template->assign_var('SITE_BG_IMG', $site_background_logo);
				}
			}

			if ($this->user->page['page_name'] != "viewforum.$this->php_ext" && $this->user->page['page_name'] != "viewtopic.$this->php_ext" && $this->config['chi_showpagename'])
			{
				$chi_showpagename = str_replace(['.php'], '', $this->user->page['page']);
				$chi_showpagenames = str_replace(['?'], ' ', $chi_showpagename);

				$this->template->assign_vars([
					'S_CHI_SHOWPAGENAME'		=> true,
					'CHI_SHOWPAGENAME'			=> $chi_showpagenames,
				]);
			}

			$this->template->assign_vars([
				'CHI_WIDTH_SET'				=> $this->config['chi_width_set'],
				'CHI_HEIGHT_SET'			=> $this->config['chi_height_set'],
				'S_CHI_ENABLED'				=> $this->config['chi_enable'] && ($this->config['chi_enable_guests'] && empty($this->user->data['is_registered']) || !empty($this->user->data['is_registered'])),
			]);
		}
	}
}
