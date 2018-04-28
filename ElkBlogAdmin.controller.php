<?php

class ElkBlogAdmin_Controller extends Action_Controller
{
	public function action_index()
	{
		require_once(SUBSDIR . '/Action.class.php');
		// Where do you want to go today?
		$subActions = array(
			'index' => array($this, 'action_admin'),
			'edit' 	=> array($this, 'action_edit'),
		);
		// We like action, so lets get ready for some
		$action = new Action('');
		// Get the subAction, or just go to action_sportal_index
		$subAction = $action->initialize($subActions, 'index');
		// Finally go to where we want to go
		$action->dispatch($subAction);
	}

	public function action_admin()
	{
		global $context;
		$context['sub_template'] = 'elkblog_admin';
		loadTemplate('ElkBlogAdmin');
	}

	public function action_edit() 
	{
		global $context;
		if (!empty($_POST['blog_subject']) && !empty($_POST['blog_body'])) {
			die(var_export($_POST, TRUE));
		}

		if (checkSession('post', '', false) !== '') {
			return;
		}

		$context['blog_subject'] 	= $_POST['blog_subject'];
		$context['blog_body'] 		= $_POST['blog_body'];

		$context['sub_template'] = 'elkblog_admin';
		loadTemplate('ElkBlogAdmin');

	}
}
