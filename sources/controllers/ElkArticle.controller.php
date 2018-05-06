<?php

use ElkArte\sources\Frontpage_Interface;

class ElkArticle_Controller extends Action_Controller implements Frontpage_Interface
{
	public function action_index()
	{
		require_once(SUBSDIR . '/Action.class.php');
		// Where do you want to go today?
		$subActions = array(
			'index' => array($this, 'action_elkarticle'),
		);
		// We like action, so lets get ready for some
		$action = new Action('');
		// Get the subAction, or just go to action_sportal_index
		$subAction = $action->initialize($subActions, 'index');
		// Finally go to where we want to go
		$action->dispatch($subAction);
	}

	public function action_elkarticle()
	{
		global $context, $scripturl;
		
		require_once(SUBSDIR . '/ElkArticle.subs.php');	

		$context['page_title']		= $context['forum_name'];
		$context['sub_template'] 	= 'elkarticle';

		// Set up for pagination
		$start 		= !empty($_REQUEST['start']) ? (int) $_REQUEST['start'] : 0;
		$per_page	= 10;
		$articles	= get_articles($start, $per_page);	
		$total_articles = get_total_articles(); 

		$context['articles'] 		= $articles;
		$context['page_index'] 		= constructPageIndex($scripturl . '?action=home;start=%1$d', $start, $total_articles, $per_page, true);

		loadTemplate('ElkArticle');
	}

	public static function canFrontPage()
	{
		return true;
	}

	public static function frontPageHook(&$default_action)
	{
		// View the portal front page
		$file = CONTROLLERDIR . '/ElkArticle.controller.php';
		$controller = 'ElkArticle_Controller';
		$function = 'action_index';
		// Something article-ish, then set the new action
		if (isset($file, $function)) {
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
