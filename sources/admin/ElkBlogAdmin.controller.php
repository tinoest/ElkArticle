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
		$context['sub_template'] = 'elkblog_list';
		loadTemplate('ElkBlogAdmin');
	}

	public function action_edit() 
	{
		global $context, $user_info;

		if (!empty($_POST['blog_subject']) && !empty($_POST['blog_body']) && empty($_POST['blog_id'])) {
			if (checkSession('post', '', false) !== '') {
				return;
			}
			$db = database();
			$db->insert('', 
				'{db_prefix}blog_articles',
				array( 
					'member_id' 	=> 'int',
					'title'		=> 'string',
					'body'		=> 'string',
					'dt_published'	=> 'int',
				),
				array (
					$user_info['id'],
					$_POST['blog_subject'],
					$_POST['blog_body'],
					time(),
				),
				array('id')
			);
			$context['blog_id'] 		= $db->insert_id('{db_prefix}blog_articles', 'id');
			$context['blog_subject'] 	= $_POST['blog_subject'];
			$context['blog_body'] 		= $_POST['blog_body'];
		}
		else if (!empty($_POST['blog_subject']) && !empty($_POST['blog_body']) && !empty($_POST['blog_id'])) {
			if (checkSession('post', '', false) !== '') {
				return;
			}
			$db = database();
			$db->query('', '
				UPDATE {db_prefix}blog_articles
				SET title = {string:title}, body = {string:body}
				WHERE id = {int:id}',
				array (
					'title' => $_POST['blog_subject'],
					'body'	=> $_POST['blog_body'],
					'id'	=> $_POST['blog_id'],
				)
			);

			$context['blog_id'] 		= $_POST['blog_id'];
			$context['blog_subject'] 	= $_POST['blog_subject'];
			$context['blog_body'] 		= $_POST['blog_body'];
		}

	
		$context['sub_template'] 	= 'elkblog_edit';
		loadTemplate('ElkBlogAdmin');
	}
}
