<?php

use ElkArte\sources\Frontpage_Interface;

class ElkBlog_Controller extends Action_Controller implements Frontpage_Interface
{
	public function action_index()
	{
		require_once(SUBSDIR . '/Action.class.php');
		// Where do you want to go today?
		$subActions = array(
			'index' => array($this, 'action_elkblog'),
		);
		// We like action, so lets get ready for some
		$action = new Action('');
		// Get the subAction, or just go to action_sportal_index
		$subAction = $action->initialize($subActions, 'index');
		// Finally go to where we want to go
		$action->dispatch($subAction);
	}

	public function action_elkblog()
	{
		global $context;
		$context['page_title']		= 'Elk Blog';
		$context['sub_template'] 	= 'elkblog';
		loadTemplate('ElkBlog');
	}

	public static function canFrontPage()
	{
		return true;
	}

	public static function frontPageHook(&$default_action)
	{
		// View the portal front page
		$file = CONTROLLERDIR . '/ElkBlog.controller.php';
		$controller = 'ElkBlog_Controller';
		$function = 'action_index';
		// Something portal-ish, then set the new action
		if (isset($file, $function))
		{
			$default_action = array(
				'file' => $file,
				'controller' => isset($controller) ? $controller : null,
				'function' => $function
			);
		}
	}

	public static function frontPageOptions()
	{

	}

}
