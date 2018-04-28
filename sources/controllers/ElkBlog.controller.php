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
		
		$db 		= database();
		$request 	= $db->query('', '
			SELECT id, name
			FROM {db_prefix}blog_categories
			WHERE status = 1'
		);
		
		while ($row = $db->fetch_assoc($request))
		{
			$categories[$row['id']] = $row['name'];
		}
	
		$request 	= $db->query('', '
			SELECT category_id, member_id, dt_published, title, body, views, comments
			FROM {db_prefix}blog_articles
			WHERE status = 1'
		);

		$articles 	= array();
		while ($row = $db->fetch_assoc($request))
		{
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

		$context['articles'] = $articles;

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
