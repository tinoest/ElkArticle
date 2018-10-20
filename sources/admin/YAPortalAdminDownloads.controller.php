<?php

/**
 * @package "YAPortal" Addon for Elkarte
 * @author tinoest
 * @license BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * @version 1.0.0
 *
 */

if (!defined('ELK'))
{
	die('No access...');
}

class YAPortalAdminDownloads_Controller extends Action_Controller
{
	public function action_index()
	{

        if (!allowedTo('yaportal_admin')) {
            isAllowedTo('yaportal_manage_settings');
        }

		require_once(SUBSDIR . '/Action.class.php');
		// Where do you want to go today?
		$subActions = array(
			'index' 		    => array($this, 'action_default'),
			'listdownload' 		=> array($this, 'action_list_download'),
			'adddownload' 		=> array($this, 'action_edit_download'),
			'editdownload' 		=> array($this, 'action_edit_download'),
			'deletedownload'	=> array($this, 'action_delete_download'),
			'listcategory' 		=> array($this, 'action_list_category'),
			'addcategory' 		=> array($this, 'action_add_category'),
			'editcategory' 		=> array($this, 'action_edit_category'),
			'deletecategory' 	=> array($this, 'action_delete_category'),
			'uploaddownload' 	=> array($this, 'action_upload_download'),
			'removedownload' 	=> array($this, 'action_remove_download'),
		);
		// We like action, so lets get ready for some
		$action = new Action('');
		// Get the subAction, or just go to action_index
		$subAction = $action->initialize($subActions, 'index');
		// Finally go to where we want to go

		$action->dispatch($subAction);
	}

	public function action_default()
	{
		$this->action_list_download();
	}

	public function action_admin_menu()
	{
		global $context, $txt;

		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $txt['yaportal-title'],
			'help' => '',
			'description' => $txt['yaportal-desc'],
			'tabs' => array(
				'listdownload' 	=> array(),
				'listcategory' 	=> array(),
			),
		);
	}

	public function action_list_download()
	{
		global $context, $scripturl, $txt;

		$this->action_admin_menu();

		$list = array (
			'id' => 'download_list',
			'title' => $txt['yaportal-downloads'],
			'items_per_page' => 25,
			'no_items_label' => $txt['yaportal-notfound'],
			'base_href' => $scripturl . '?action=admin;area=yaportaldownload;sa=listdownload;',
			'default_sort_col' => 'title',
			'get_items' => array(
				'function' => array($this, 'list_downloads'),
			),
			'get_count' => array(
				'function' => array($this, 'list_total_downloads'),
			),
			'columns' => array(
				'title' => array(
					'header' => array(
						'value' => 'Title',
					),
					'data' => array(
						'db' => 'title',
					),
					'sort' => array(
						'default' => 'title ASC',
						'reverse' => 'title DESC',
					),
				),

				'category' => array(
					'header' => array(
						'value' => 'Category',
					),
					'data' => array(
						'db' => 'category',
					),
					'sort' => array(
						'default' => 'category_id ASC',
						'reverse' => 'category_id DESC',
					),
				),
				'author' => array(
					'header' => array(
						'value' => 'Author',
					),
					'data' => array(
						'db' => 'member',
					),
					'sort' => array(
						'default' => 'member_id ASC',
						'reverse' => 'member_id DESC',
					),
				),
				'date' => array(
					'header' => array(
						'value' => 'Date Published',
					),
					'data' => array(
						'db' => 'dt_published',
					),
					'sort' => array(
						'default' => 'dt_published ASC',
						'reverse' => 'dt_published DESC',
					),
				),
				'status' => array(
					'header' => array(
						'value' => 'Status',
						'class' => 'centertext',
					),
					'data' => array(
						'db' => 'status',
						'class' => 'centertext',
					),
					'sort' => array(
						'default' => 'status',
						'reverse' => 'status DESC',
					),
				),
				'action' => array(
					'header' => array(
						'value' => 'Actions',
						'class' => 'centertext',
					),
					'data' => array(
						'sprintf' => array (
							'format' => '
								<a href="?action=admin;area=yaportaldownload;sa=editdownload;id=%1$d;' . $context['session_var'] . '=' . $context['session_id'] . '" accesskey="p">Modify</a>&nbsp;
								<a href="?action=admin;area=yaportaldownload;sa=deletedownload;id=%1$d;' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="return confirm(' . JavaScriptEscape('Are you sure you want to delete?') . ') && submitThisOnce(this);" accesskey="d">Delete</a>',
							'params' => array(
								'id' => true,
							),
						),
						'class' => 'centertext nowrap',
					),
				),
			),
			'form' => array(
				'href' => $scripturl . '?action=admin;area=yaportaldownload;sa=adddownload',
				'include_sort' => true,
				'include_start' => true,
				'hidden_fields' => array(
					$context['session_var'] => $context['session_id'],
				),
			),
			'additional_rows' => array(
				array(
					'position' => 'below_table_data',
					'value' => '<input type="submit" name="action_edit" value="' . $txt['yaportal-adddownload'] . '" class="right_submit" />',
				),
			),
		);

		$context['page_title']		= 'Article List';
		$context['sub_template'] 	= 'yaportal_list';
		$context['default_list'] 	= 'download_list';
		// Create the list.
		require_once(SUBSDIR . '/GenericList.class.php');
		createList($list);
		loadTemplate('YAPortalAdminDownloads');
	}

	public function action_edit_download()
	{
		global $context, $user_info, $boardurl;

		require_once(SUBSDIR . '/YAPortalDownloads.subs.php');
		require_once(SUBSDIR . '/YAPortalAdminDownloads.subs.php');

		// Set the defaults
		$context['download_category']	= 1;
		$context['download_subject'] 	= '';
		$context['download_body'] 	    = '';
        $context['download_link']       = '';
        $download_link                  = '';

        if(!empty($_FILES['download_link'])) {
            if(!empty($_FILES['download_link']['tmp_name'])) {
                if(in_array(pathinfo($_FILES['download_link']['name'], PATHINFO_EXTENSION), array( 'zip', 'gz', 'tar.gz', 'pdf' ) ) ) {
                    move_uploaded_file($_FILES['download_link']['tmp_name'], BOARDDIR . '/yaportal/downloads/' . $_FILES['download_link']['name']);
                }
                $download_link                 = $_FILES['download_link']['name'];
            }
        }

		$status				            = 1;
		if(array_key_exists('download_status', $_POST)) {
			$status			            = $_POST['download_status'];
		}
		$context['download_status']	    = $status;

		if (!empty($_POST['download_subject']) && !empty($_POST['download_body']) && empty($_POST['id'])) {
			if (checkSession('post', '', false) !== '') {
				return;
			}

			$subject			        = $_POST['download_subject'];
			$body				        = $_POST['download_body'];
			$category_id			    = $_POST['download_category'];

			$context['download_id']		= insert_download($subject, $body, $category_id, $user_info['id'], $download_link, $status);
			$context['download_subject'] = $subject;
			$context['download_body'] 	= $body;
			$context['download_category']= $category_id;
			$context['download_link']   = $download_link;
		}
		else if ( (!empty($_POST['download_subject']) || !empty($_POST['download_body'])) && !empty($_POST['download_category']) && !empty($_POST['id'])) {
			if (checkSession('post', '', false) !== '') {
				return;
			}

			$subject			= $_POST['download_subject'];
			if(array_key_exists('download_body', $_POST) && !empty($_POST['download_body'])) {
				$body			= $_POST['download_body'];
			}
			else {
				$body			= null;
			}
			$category_id		= $_POST['download_category'];
			$download_id	 		= $_POST['id'];

            if(!empty($download_link)) {
                // Are we changing the download? remove the old one if we are
			    $download_data	    = get_download($download_id);
                if($download_data['download_link'] != $download_link) {
                    $fileName   = BOARDDIR . '/yaportal/downloads/' . $download_data['download_link'];
                    if(file_exists( $fileName ) && !is_dir( $fileName )) {
                        unlink( $fileName );
                    }
                }
            }

			update_download($subject, $body, $category_id, $download_id, $download_link, $status);

			$download_data			    = get_download($download_id);
			$context['download_id'] 		= $download_data['id'];
			$context['download_subject'] = $download_data['title'];
			$context['download_body'] 	= $download_data['body'];
			$context['download_category']= $download_data['category_id'];
			$context['download_status']	= $download_data['status'];
			$context['download_link']   = $download_data['download_link'];
		}
		else if (!empty($_GET['id'])) {
			if (checkSession('get', '', false) !== '') {
				return;
			}

			$download_id	 		        = $_GET['id'];
			$download_data			    = get_download($download_id);
			$context['download_id'] 		= $download_data['id'];
			$context['download_subject'] = $download_data['title'];
			$context['download_body'] 	= $download_data['body'];
			$context['download_category']= $download_data['category_id'];
			$context['download_status']	= $download_data['status'];
			$context['download_link']   = $download_data['download_link'];
		}

        $context['download_link_src']   = $boardurl . '/yaportal/downloads/' . $context['download_link'];
        $context['download_download_link']  = urlencode($context['download_link']);

		$context['download_categories']	= get_download_categories();

		$context['sub_template'] 	    = 'yaportal_edit';

		loadTemplate('YAPortalAdminDownloads');
	}

	public function action_delete_download()
	{
		require_once(SUBSDIR . '/YAPortalAdminDownloads.subs.php');
		require_once(SUBSDIR . '/YAPortalDownloads.subs.php');


		if (!empty($_GET['id'])) {
			if (checkSession('get', '', false) !== '') {
				return;
			}

			$id	        =  $_GET['id'];
            $download    = get_download($id);
            $fileName   = BOARDDIR . '/yaportal/downloads/' . $download['download_link'];

			delete_download($id);

            if(file_exists( $fileName )) {
                unlink( $fileName );
            }
		}

		// Just Load the list again
		$this->action_list_download();
	}

	public function action_list_category()
	{
		global $context, $scripturl, $txt;

		$this->action_admin_menu();

		$list = array (
			'id' => 'category_list',
			'title' => $txt['yaportal-categories'],
			'items_per_page' => 25,
			'no_items_label' => $txt['yaportal-notfound'],
			'base_href' => $scripturl . '?action=admin;area=yaportaldownload;sa=listcategory;',
			'default_sort_col' => 'name',
			'get_items' => array (
				'function' => array($this, 'list_categories'),
			),
			'get_count' => array (
				'function' => array($this, 'list_total_categories'),
			),
			'columns' => array (
				'name' => array (
					'header' => array (
						'value' => 'Name',
					),
					'data' => array (
						'db' => 'name',
					),
					'sort' => array (
						'default' => 'name ASC',
						'reverse' => 'name DESC',
					),
				),
				'description' => array(
					'header' => array(
						'value' => 'Description',
					),
					'data' => array(
						'db' => 'description',
					),
					'sort' => array(
						'default' => 'description ASC',
						'reverse' => 'description DESC',
					),
				),
				'ariticles' => array(
					'header' => array(
						'value' => 'Downloads',
					),
					'data' => array(
						'db' => 'downloads',
					),
					'sort' => array(
						'default' => 'downloads ASC',
						'reverse' => 'downloads DESC',
					),
				),
				'status' => array(
					'header' => array(
						'value' => 'Status',
						'class' => 'centertext',
					),
					'data' => array(
						'db' => 'status',
						'class' => 'centertext',
					),
					'sort' => array(
						'default' => 'status ASC',
						'reverse' => 'status DESC',
					),
				),
				'action' => array(
					'header' => array(
						'value' => 'Actions',
						'class' => 'centertext',
					),
					'data' => array(
						'sprintf' => array (
							'format' => '
								<a href="?action=admin;area=yaportaldownload;sa=editcategory;category_id=%1$s;' . $context['session_var'] . '=' . $context['session_id'] . '" accesskey="p">Modify</a>&nbsp;
								<a href="?action=admin;area=yaportaldownload;sa=deletecategory;category_id=%1$s;' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="return confirm(' . JavaScriptEscape('Are you sure you want to delete?') . ') && submitThisOnce(this);" accesskey="d">Delete</a>',
							'params' => array(
								'id' => true,
							),
						),
						'class' => 'centertext nowrap',
					),
				),
			),
			'form' => array(
				'href' => $scripturl . '?action=admin;area=yaportaldownload;sa=addcategory',
				'include_sort' => true,
				'include_start' => true,
				'hidden_fields' => array(
					$context['session_var'] => $context['session_id'],
				),
			),
			'additional_rows' => array(
				array(
					'position' => 'below_table_data',
					'value' => '<input type="submit" name="action_add_category" value="' . $txt['yaportal-addcategory'] . '" class="right_submit" />',
				),
			),
		);

		$context['page_title']		= 'Category List';
		$context['sub_template'] 	= 'elkcategory_list';
		$context['default_list'] 	= 'category_list';
		// Create the list.
		require_once(SUBSDIR . '/GenericList.class.php');
		createList($list);
		loadTemplate('YAPortalAdminDownloads');
	}

	public function action_add_category()
	{
		global $context;

		if(!empty($_POST['category_name'])) {
			if (checkSession('post', '', false) !== '') {
				return;
			}
			require_once(SUBSDIR . '/YAPortalAdminDownloads.subs.php');
			$name 	= $_POST['category_name'];
			$desc 	= $_POST['category_desc'];
			if(!empty($_POST['category_enabled'])) {
				$status = 1;
			}
			else {
				$status	= 0;
			}
			insert_category($name, $desc, $status);
			$this->action_list_category();
		}
		else {
			$context['page_title']		= 'Add Category';
			$context['sub_template'] 	= 'elkcategory_add';
			loadTemplate('YAPortalAdminDownloads');
		}
	}

	public function action_edit_category()
	{
		global $context;


		if ( !empty($_POST['category_id']) && !empty($_POST['category_name']) && !empty($_POST['category_desc']) ) {
			if (checkSession('post', '', false) !== '') {
				return;
			}
			require_once(SUBSDIR . '/YAPortalAdminDownloads.subs.php');

			$category_id			= $_POST['category_id'];
			$category_name			= $_POST['category_name'];
			$category_desc			= $_POST['category_desc'];
			if(!empty($_POST['category_enabled'])) {
				$category_enabled 	= 1;
			}
			else {
				$category_enabled	= 0;
			}
			update_category($category_id, $category_name, $category_desc, $category_enabled);

			$context['category_id']		= $category_id;
			$context['category_name']	= $category_name;
			$context['category_desc']	= $category_desc;
			$context['category_enabled']	= $category_enabled;
		}
		else if(!empty($_GET['category_id'])) {
			if (checkSession('get', '', false) !== '') {
				return;
			}
			require_once(SUBSDIR . '/YAPortalDownloads.subs.php');
			$category_id			= $_GET['category_id'];
			$category_details		= get_category($category_id);
			$context['category_id']		= $category_details['id'];
			$context['category_name']	= $category_details['name'];
			$context['category_desc']	= $category_details['description'];
			$context['category_enabled']	= $category_details['enabled'];

			$context['page_title']		= 'Edit Category';
			$context['sub_template'] 	= 'elkcategory_edit';
			loadTemplate('YAPortalAdminDownloads');
			return;
		}

		$this->action_list_category();
	}

	public function action_delete_category()
	{
		require_once(SUBSDIR . '/YAPortalAdminDownloads.subs.php');
		if (!empty($_GET['category_id'])) {
			if (checkSession('get', '', false) !== '') {
				return;
			}

			$id	=  $_GET['category_id'];
			delete_category($id);
		}

		// Just Load the list again
		$this->action_list_category();
	}

    public function action_upload_download()
    {

    }

    public function action_remove_download()
    {

    }

	public function list_downloads($start, $items_per_page, $sort)
	{
		require_once(SUBSDIR . '/YAPortalAdminDownloads.subs.php');
		return get_downloads_list($start, $items_per_page, $sort);
	}

	public function list_total_downloads()
	{
		require_once(SUBSDIR . '/YAPortalDownloads.subs.php');
		return get_total_downloads();
	}

	public function list_categories($start, $items_per_page, $sort)
	{
		require_once(SUBSDIR . '/YAPortalDownloads.subs.php');
		return get_category_list($start, $items_per_page, $sort);
	}

	public function list_total_categories()
	{
		require_once(SUBSDIR . '/YAPortalDownloads.subs.php');
		return get_total_categories();
	}
}
