<?php
/**
*
* @package phpBB Extension - Custom Header Logo
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\chl\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
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
	* @param string 							$phpbb_root_path
	* @param string 							$phpEx
	* @param string 							$header_images_table
	*
	*/

	public function __construct(\phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, $request, $phpbb_root_path, $phpEx, $header_images_table)
	{
		$this->config 				= $config;
		$this->template 			= $template;
		$this->user 				= $user;
		$this->db 					= $db;
		$this->request 				= $request;
		$this->phpbb_root_path 		= $phpbb_root_path;
		$this->phpEx 				= $phpEx;
		$this->header_images_table 	= $header_images_table;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.page_header_after'	=> 'add_page_header_link',
		);
	}

	public function add_page_header_link($event)
	{
		$site_logo = $this->user->img('site_logo');
		$site_background_logo = '';

		if (isset($this->config['chi_enable']))
		{
			if ($this->config['chi_enable'] && ($this->config['chi_enable_guests'] && empty($this->user->data['is_registered']) || !empty($this->user->data['is_registered'])))
			{
				$post = $this->request->get_super_global(\phpbb\request\request::REQUEST);
				// Are we in a forum, topic or a posting page with logo/bg logo?
				if ((($this->user->page['page_name'] == "viewforum.$this->phpEx") || ($this->user->page['page_name'] == "viewtopic.$this->phpEx") || ($this->user->page['page_name'] == "posting.$this->phpEx")) && !empty($post['f']) && !is_array($post['f']))
				{
					$forum_id = $this->request->variable('f', 0);
					$sql = 'SELECT page_logo, page_background_logo
						FROM ' . $this->header_images_table . '
						WHERE forum_id = ' . (int) $forum_id;
					$result = $this->db->sql_query($sql);
					$forum_logo = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);
					$site_logo_custom = $forum_logo['page_logo'];
					$header_background_custom = $forum_logo['page_background_logo'];
				}

				// Are we on a page where we defined a logo and/or header background image?
				if ($this->user->page['page_name'] != "viewforum.$this->phpEx")
				{
					$sql_where = "page_name = '" . $this->db->sql_escape(str_replace('.' . $this->phpEx, '', $this->user->page['page_name'])) . "' AND page_path = '" . $this->user->page['page_dir'] . "' AND forum_id = 0";

					$sql = 'SELECT page_logo, page_background_logo, page_query
						FROM ' . $this->header_images_table . "
						WHERE $sql_where
						ORDER BY page_query DESC, page_path DESC";
					$result = $this->db->sql_query($sql);

					while ($row = $this->db->sql_fetchrow($result))
					{
						if (stripos($this->user->page['query_string'],$row['page_query']) !== false || $row['page_query'] == '*' || $row['page_query'] == $this->user->page['query_string'])
						{
							$site_logo_custom = $row['page_logo'];
							$header_background_custom = $row['page_background_logo'];
							break;
						}
					}
					$this->db->sql_freeresult($result);
				}

				// Create the link(s)
				if (!empty($site_logo_custom))
				{
					$site_logo = '<img src="' . $this->phpbb_root_path . 'ext/dmzx/chl/images/logos/' . $site_logo_custom . '" alt="" title="" />';
				}
				if (!empty($header_background_custom))
				{
					$site_background_logo = $this->phpbb_root_path . 'ext/dmzx/chl/images/backgrounds/' . $header_background_custom;
				}
			}
		}
		$this->template->assign_vars(array(
			'SITE_LOGO_IMG'		=> $site_logo,
			'SITE_BG_IMG'			=> $site_background_logo,
		));
	}
}

