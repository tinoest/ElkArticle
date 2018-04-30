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
		global $context;

		$db = database();

		$context['sub_template']	= 'elkblog_list';
		
		$request 	= $db->query('', '
			SELECT category_id, member_id, dt_published, title
			FROM {db_prefix}blog_articles
			WHERE status = 1
			ORDER BY id DESC'
		);

		$articles 	= array();
		while ($row = $db->fetch_assoc($request)) {
			$member	= $db->query('', '
				SELECT member_name
				FROM {db_prefix}members
				WHERE id_member = {int:member_id}',
				array ( 
					'member_id' => $row['member_id'],
				)
			);
			$row['member'] 		= $db->fetch_assoc($member)['member_name'];
			$row['category']	= $categories[$row['category_id']];	
			$articles[] 		= $row; 
		}

		$context['blog_articles'] = $articles;

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
}
