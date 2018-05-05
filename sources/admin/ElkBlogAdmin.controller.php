<?php

class ElkBlogAdmin_Controller extends Action_Controller
{
	public function action_index()
	{
		require_once(SUBSDIR . '/Action.class.php');
		// Where do you want to go today?
		$subActions = array(
			'index' 	=> array($this, 'action_list'),
			'listarticle' 	=> array($this, 'action_list'),
			'editarticle' 	=> array($this, 'action_edit'),
		);
		// We like action, so lets get ready for some
		$action = new Action('');
		// Get the subAction, or just go to action_portal_index
		$subAction = $action->initialize($subActions, 'index');
		// Finally go to where we want to go

		$action->dispatch($subAction);
	}

	public function action_list()
	{
		global $context, $scripturl, $txt;

		$list = array (
			'id' => 'blog_articles_list',
			'title' => 'Blog Articles',
			'items_per_page' => 25,
			'no_items_label' => 'No Articles Found',
			'base_href' => $scripturl . '?action=admin;area=blogconfig;sa=listarticle;',
			'default_sort_col' => 'title',
			'get_items' => array(
				'function' => array($this, 'list_blog_articles'),
			),
			'get_count' => array(
				'function' => array($this, 'list_total_articles'),
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
						'sprintf' => array(
							'format' => '<a href="?action=admin;area=blogconfig;sa=editarticle">Modify</a>&nbsp;',
							'params' => array(
								'id' => true,
							),
						),
						'class' => 'centertext nowrap',
					),
				),
			),
			'form' => array(
				'href' => $scripturl . '?action=admin;area=blogconfig;sa=deletearticle',
				'include_sort' => true,
				'include_start' => true,
				'hidden_fields' => array(
					$context['session_var'] => $context['session_id'],
				),
			),
			'additional_rows' => array(
				array(
					'position' => 'below_table_data',
					'value' => '<input type="submit" name="remove_articles" value="remove_blog_article" class="right_submit" />',
				),
			),
		);	
	
		$context['page_title']		= 'Blog List';
		$context['sub_template'] 	= 'elkblog_list';	
		$context['default_list'] 	= 'blog_list';
		// Create the list.
		require_once(SUBSDIR . '/GenericList.class.php');
		createList($list);
		loadTemplate('ElkBlogAdmin');
	}

	public function action_edit() 
	{
		global $context, $user_info;

		require_once(SUBSDIR . '/ElkBlog.subs.php');
		require_once(SUBSDIR . '/ElkBlogAdmin.subs.php');

		// Set the defaults
		$context['blog_category']	= 1;
		$context['blog_subject'] 	= '';
		$context['blog_body'] 		= '';

		if (!empty($_POST['blog_subject']) && !empty($_POST['blog_body']) && empty($_POST['blog_id'])) {
			if (checkSession('post', '', false) !== '') {
				return;
			}

			$subject			= $_POST['blog_subject'];
			$body				= $_POST['blog_body'];
			$category_id			= $_POST['blog_category'];

			$context['blog_id']		= insert_blog_article($subject, $body, $category_id, $user_info['id']);
			$context['blog_subject'] 	= $subject;
			$context['blog_body'] 		= $body;
			$context['blog_category']	= $category_id;
		}
		else if (!empty($_POST['blog_subject']) && !empty($_POST['blog_body']) && !empty($_POST['blog_id'])) {
			if (checkSession('post', '', false) !== '') {
				return;
			}
	
			$subject			= $_POST['blog_subject'];
			$body				= $_POST['blog_body'];
			$category_id			= $_POST['blog_category'];
			$blog_id	 		= $_POST['blog_id'];

			update_blog_article($subject, $body, $category_id, $blog_id);

			$context['blog_id'] 		= $blog_id;
			$context['blog_subject'] 	= $subject;
			$context['blog_body'] 		= $body;
			$context['blog_category']	= $category_id;
		}

		$context['blog_categories']	= get_blog_categories();
	
		$context['sub_template'] 	= 'elkblog_edit';
		loadTemplate('ElkBlogAdmin');
	}

	public function list_blog_articles()
	{
		require_once(SUBSDIR . '/ElkBlogAdmin.subs.php');
		return get_blog_articles_list();
	}
 
	public function list_total_articles()
	{
		require_once(SUBSDIR . '/ElkBlog.subs.php');
		return get_total_blog_articles();
	} 
}
