<?php

class ElkBlogAdmin_Controller extends Action_Controller
{
	public function action_index()
	{
		require_once(SUBSDIR . '/Action.class.php');
		// Where do you want to go today?
		$subActions = array(
			'index' => array($this, 'action_admin'),
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

}
