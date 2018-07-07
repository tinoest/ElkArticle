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

class YAPortalAdminGallery_Controller extends Action_Controller
{
	public function action_index()
	{
		require_once(SUBSDIR . '/Action.class.php');
		// Where do you want to go today?
		$subActions = array(
			'index' 		    => array($this, 'action_default'),
			'listgallery' 		=> array($this, 'action_list_gallery'),
			'addgallery' 		=> array($this, 'action_edit_gallery'),
			'editgallery' 		=> array($this, 'action_edit_gallery'),
			'deletegallery'		=> array($this, 'action_delete_gallery'),
			'listcategory' 		=> array($this, 'action_list_category'),
			'addcategory' 		=> array($this, 'action_add_category'),
			'editcategory' 		=> array($this, 'action_edit_category'),
			'deletecategory' 	=> array($this, 'action_delete_category'),
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
		$this->action_list_gallery();
	}

	public function action_admin_menu()
	{
		global $context, $txt;

		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $txt['yaportal-title'],
			'help' => '',
			'description' => $txt['yaportal-desc'],
			'tabs' => array(
				'listgallery' 	=> array(),
				'listcategory' 	=> array(),
			),
		);
	}	

	public function action_list_gallery()
	{
		global $context, $scripturl, $txt;

		$this->action_admin_menu();

		$list = array (
			'id' => 'gallery_list',
			'title' => $txt['yaportal-galleries'],
			'items_per_page' => 25,
			'no_items_label' => $txt['yaportal-notfound'],
			'base_href' => $scripturl . '?action=admin;area=yaportalgallery;sa=listgallery;',
			'default_sort_col' => 'title',
			'get_items' => array(
				'function' => array($this, 'list_galleries'),
			),
			'get_count' => array(
				'function' => array($this, 'list_total_galleries'),
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
								<a href="?action=admin;area=yaportalgallery;sa=editgallery;gallery_id=%1$s;' . $context['session_var'] . '=' . $context['session_id'] . '" accesskey="p">Modify</a>&nbsp;
								<a href="?action=admin;area=yaportalgallery;sa=deletegallery;gallery_id=%1$s;' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="return confirm(' . JavaScriptEscape('Are you sure you want to delete?') . ') && submitThisOnce(this);" accesskey="d">Delete</a>',
							'params' => array(
								'id' => true,
							),
						),
						'class' => 'centertext nowrap',
					),
				),
			),
			'form' => array(
				'href' => $scripturl . '?action=admin;area=yaportalgallery;sa=addgallery',
				'include_sort' => true,
				'include_start' => true,
				'hidden_fields' => array(
					$context['session_var'] => $context['session_id'],
				),
			),
			'additional_rows' => array(
				array(
					'position' => 'below_table_data',
					'value' => '<input type="submit" name="action_edit" value="' . $txt['yaportal-addgallery'] . '" class="right_submit" />',
				),
			),
		);
	
		$context['page_title']		= 'Article List';
		$context['sub_template'] 	= 'yaportal_list';	
		$context['default_list'] 	= 'gallery_list';
		// Create the list.
		require_once(SUBSDIR . '/GenericList.class.php');
		createList($list);
		loadTemplate('YAPortalAdminGallery');
	}

	public function action_edit_gallery() 
	{
		global $context, $user_info, $boardurl;

		require_once(SUBSDIR . '/YAPortalGallery.subs.php');
		require_once(SUBSDIR . '/YAPortalAdminGallery.subs.php');

		// Set the defaults
		$context['gallery_category']	= 1;
		$context['gallery_subject'] 	= '';
		$context['gallery_body'] 	    = '';
        $context['gallery_image']       = '';
        $image_name                     = null;

        if(!empty($_FILES['gallery_image'])) {
            if(in_array(exif_imagetype($_FILES['gallery_image']['tmp_name']), array( IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG ) ) ) {
                move_uploaded_file($_FILES['gallery_image']['tmp_name'], BOARDDIR . '/yaportal/img/' . $_FILES['gallery_image']['name']);
            }
            $image_name                 = $_FILES['gallery_image']['name'];
        }

		$status				            = 1;
		if(array_key_exists('gallery_status', $_POST)) {
			$status			            = $_POST['gallery_status'];
		}
		$context['gallery_status']	    = $status;

		if (!empty($_POST['gallery_subject']) && !empty($_POST['gallery_body']) && empty($_POST['gallery_id'])) {
			if (checkSession('post', '', false) !== '') {
				return;
			}

			$subject			        = $_POST['gallery_subject'];
			$body				        = $_POST['gallery_body'];
			$category_id			    = $_POST['gallery_category'];

			$context['gallery_id']		= insert_gallery($subject, $body, $category_id, $user_info['id'], $image_name, $status);
			$context['gallery_subject'] = $subject;
			$context['gallery_body'] 	= $body;
			$context['gallery_category']= $category_id;
			$context['gallery_image']   = $image_name;
		}
		else if ( (!empty($_POST['gallery_subject']) || !empty($_POST['gallery_body'])) && !empty($_POST['gallery_category']) && !empty($_POST['gallery_id'])) {
			if (checkSession('post', '', false) !== '') {
				return;
			}
	
			$subject			= $_POST['gallery_subject'];
			if(array_key_exists('gallery_body', $_POST) && !empty($_POST['gallery_body'])) {
				$body			= $_POST['gallery_body'];
			}
			else {
				$body			= null;
			}
			$category_id		= $_POST['gallery_category'];
			$gallery_id	 		= $_POST['gallery_id'];

            if(!empty($image_name)) {
                // Are we changing the image? remove the old one if we are
			    $gallery_data	    = get_gallery($gallery_id);
                if($gallery_data['image_name'] != $image_name) {
                    $fileName   = BOARDDIR . '/yaportal/img/' . $gallery_data['image_name'];
                    if(file_exists( $fileName )) {
                        unlink( $fileName );
                    }                   
                }
            }

			update_gallery($subject, $body, $category_id, $gallery_id, $image_name, $status);

			$gallery_data			    = get_gallery($gallery_id);
			$context['gallery_id'] 		= $gallery_data['id'];
			$context['gallery_subject'] = $gallery_data['title'];
			$context['gallery_body'] 	= $gallery_data['body'];
			$context['gallery_category']= $gallery_data['category_id'];
			$context['gallery_status']	= $gallery_data['status'];
			$context['gallery_image']   = $gallery_data['image_name'];
		}
		else if (!empty($_GET['gallery_id'])) {
			if (checkSession('get', '', false) !== '') {
				return;
			}
			
			$gallery_id	 		        = $_GET['gallery_id'];
			$gallery_data			    = get_gallery($gallery_id);
			$context['gallery_id'] 		= $gallery_data['id'];
			$context['gallery_subject'] = $gallery_data['title'];
			$context['gallery_body'] 	= $gallery_data['body'];
			$context['gallery_category']= $gallery_data['category_id'];
			$context['gallery_status']	= $gallery_data['status'];
			$context['gallery_image']   = $gallery_data['image_name'];
		}
			
        $context['gallery_image_src']   = $boardurl . '/yaportal/img/' . $context['gallery_image'];

		$context['gallery_categories']	= get_gallery_categories();
	
		$context['sub_template'] 	    = 'yaportal_edit';

		loadTemplate('YAPortalAdminGallery');
	}

	public function action_delete_gallery()
	{
		require_once(SUBSDIR . '/YAPortalAdminGallery.subs.php');
		require_once(SUBSDIR . '/YAPortalGallery.subs.php');


		if (!empty($_GET['gallery_id'])) {
			if (checkSession('get', '', false) !== '') {
				return;
			}
        
			$id	        =  $_GET['gallery_id'];
            $gallery    = get_gallery($id);
            $fileName   = BOARDDIR . '/yaportal/img/' . $gallery['image_name'];
			
			delete_gallery($id);

            if(file_exists( $fileName )) {
                unlink( $fileName );
            }
		}

		// Just Load the list again
		$this->action_list_gallery();
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
			'base_href' => $scripturl . '?action=admin;area=yaportalgallery;sa=listcategory;',
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
						'value' => 'Gallery',
					),
					'data' => array(
						'db' => 'galleries',
					),
					'sort' => array(
						'default' => 'galleries ASC',
						'reverse' => 'galleries DESC',
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
								<a href="?action=admin;area=yaportalgallery;sa=editcategory;category_id=%1$s;' . $context['session_var'] . '=' . $context['session_id'] . '" accesskey="p">Modify</a>&nbsp;
								<a href="?action=admin;area=yaportalgallery;sa=deletecategory;category_id=%1$s;' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="return confirm(' . JavaScriptEscape('Are you sure you want to delete?') . ') && submitThisOnce(this);" accesskey="d">Delete</a>',
							'params' => array(
								'id' => true,
							),
						),
						'class' => 'centertext nowrap',
					),
				),
			),
			'form' => array(
				'href' => $scripturl . '?action=admin;area=yaportalgallery;sa=addcategory',
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
		loadTemplate('YAPortalAdminGallery');
	}

	public function action_add_category()
	{
		global $context;

		if(!empty($_POST['category_name'])) {
			if (checkSession('post', '', false) !== '') {
				return;
			}
			require_once(SUBSDIR . '/YAPortalAdminGallery.subs.php');
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
			loadTemplate('YAPortalAdminGallery');
		}
	}

	public function action_edit_category()
	{
		global $context;


		if ( !empty($_POST['category_id']) && !empty($_POST['category_name']) && !empty($_POST['category_desc']) ) {
			if (checkSession('post', '', false) !== '') {
				return;
			}
			require_once(SUBSDIR . '/YAPortalAdminGallery.subs.php');

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
			require_once(SUBSDIR . '/YAPortalGallery.subs.php');
			$category_id			= $_GET['category_id'];
			$category_details		= get_category($category_id);
			$context['category_id']		= $category_details['id'];
			$context['category_name']	= $category_details['name'];
			$context['category_desc']	= $category_details['description'];
			$context['category_enabled']	= $category_details['enabled'];

			$context['page_title']		= 'Edit Category';
			$context['sub_template'] 	= 'elkcategory_edit';
			loadTemplate('YAPortalAdminGallery');
			return;
		} 

		$this->action_list_category();
	}

	public function action_delete_category()
	{
		require_once(SUBSDIR . '/YAPortalAdminGallery.subs.php');
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

	public function list_galleries($start, $items_per_page, $sort)
	{
		require_once(SUBSDIR . '/YAPortalAdminGallery.subs.php');
		return get_galleries_list($start, $items_per_page, $sort);
	}
 
	public function list_total_galleries()
	{
		require_once(SUBSDIR . '/YAPortalGallery.subs.php');
		return get_total_galleries();
	}

	public function list_categories($start, $items_per_page, $sort)
	{
		require_once(SUBSDIR . '/YAPortalGallery.subs.php');
		return get_category_list($start, $items_per_page, $sort);
	}
 
	public function list_total_categories()
	{
		require_once(SUBSDIR . '/YAPortalGallery.subs.php');
		return get_total_categories();
	} 
}
