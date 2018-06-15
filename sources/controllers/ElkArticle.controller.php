<?php

/**
 * @package "Elk Article" Addon for Elkarte
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

use ElkArte\sources\Frontpage_Interface;

class ElkArticle_Controller extends Action_Controller implements Frontpage_Interface
{
	public function action_index()
	{
		require_once(SUBSDIR . '/Action.class.php');
		// Where do you want to go today?
		$subActions = array(
			'index'		=> array($this, 'action_elkarticle_index'),
			'article' 	=> array($this, 'action_elkarticle'),
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
		global $context, $scripturl, $txt, $modSettings;
		loadLanguage('ElkArticle');
		loadCSSFile('elkarticle.css');
		
		require_once(SUBSDIR . '/ElkArticle.subs.php');	

		$context['page_title']		= $context['forum_name'];
		$context['sub_template'] 	= 'elkarticle';
		$article_id 			= !empty($_REQUEST['article']) ? (int) $_REQUEST['article'] : 0;
		$article			= get_article($article_id);
		if(is_array($article) && !empty($article)) {
			update_article_views($article_id);	
			$context['article'] 	= $article;
		}
		else {
			$context['article_error'] = $txt['elkarticle-not-found'];
		}
		$context['comments-enabled'] 	= $modSettings['elkarticle-enablecomments'];

		loadTemplate('ElkArticle');
	}

	public function action_elkarticle_index()
	{
		global $context, $scripturl, $modSettings;
		
		require_once(SUBSDIR . '/ElkArticle.subs.php');	

		loadCSSFile('elkarticle.css');

		$context['page_title']		= $context['forum_name'];
		$context['sub_template'] 	= 'elkarticle_index';

		// Set up for pagination
		$start 		= !empty($_REQUEST['start']) ? (int) $_REQUEST['start'] : 0;
		switch($modSettings['elkarticle-item-limit']) {
			case 0:
				$per_page = 10;
				break;
			case 1:
				$per_page = 25;
				break;
			case 2:
				$per_page = 50;
				break;
			case 3:
				$per_page = 75;
				break;
			case 4:
				$per_page = 100;
				break;
			default: 
				$per_page = 10;
				break;
		}
		$articles	= get_articles($start, $per_page);	
		$total_articles = get_total_articles(); 

		$context['comments-enabled'] 	= $modSettings['elkarticle-enablecomments'];
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
