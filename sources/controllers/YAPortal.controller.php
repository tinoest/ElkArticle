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

use ElkArte\sources\Frontpage_Interface;

class YAPortal_Controller extends Action_Controller implements Frontpage_Interface
{
	public function action_index()
	{
		require_once(SUBSDIR . '/Action.class.php');

        $subActions = array(
			'index'		=> array($this, 'action_yaportal_index'),
			'view' 	    => array($this, 'action_yaportal'),
		);

		$action     = new Action('');
		$subAction  = $action->initialize($subActions, 'index');

		$action->dispatch($subAction);
	}

	public function action_yaportal()
	{
		global $context, $scripturl, $txt, $modSettings;
		loadLanguage('YAPortal');
		loadCSSFile('yaportal.css');
		loadTemplate('YAPortalArticles');

		require_once(SUBSDIR . '/YAPortal.subs.php');

		$context['page_title']		= $context['forum_name'];
		$context['sub_template'] 	= 'yaportal_article';
		$article_id 			    = !empty($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
		$article_name 			    = !empty($_REQUEST['name']) ? (string) $_REQUEST['name'] : null;
        $article                    = array();
        if( !empty ( $article_id ) ) {
		    $article			    = get_article($article_id);
        }
        else if ( ! is_null ( $article_name ) ) {
            $article                = get_article($article_name);
        }

		if(is_array($article) && !empty($article)) {
			update_article_views($article_id);
			$context['article'] 	    = $article;
		}
		else {
			$context['article_error']   = $txt['yaportal-not-found'];
		}
        $context['comments-enabled']    = isset($modSettings['yaportal-enablecomments']) ? $modSettings['yaportal-enablecomments'] : 0;

        // Build the breadcrumbs
        $context['linktree'] = array_merge($context['linktree'], array(
            array(
                'url'   => YAPortalSEO::generateUrlString(array('action' => 'article'), true, true),
                'name'  => $txt['yaportal-articles'],
            ),
        ));

        if (!Template_Layers::getInstance()->hasLayers(true) && !in_array('yaportal', Template_Layers::getInstance()->getLayers())) {
            Template_Layers::getInstance()->add('yaportal');
        }

	}

	public function action_yaportal_index()
	{
		global $context, $scripturl, $modSettings;

		require_once(SUBSDIR . '/YAPortal.subs.php');

		loadCSSFile('yaportal.css');
        loadTemplate('YAPortal');

		$context['page_title']		= $context['forum_name'];
		$context['sub_template'] 	= 'yaportal_index';

		// Set up for pagination
		$start 		= !empty($_REQUEST['start']) ? (int) $_REQUEST['start'] : 0;
		switch($modSettings['yaportal-item-limit']) {
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

		foreach(array('topPanel', 'rightPanel', 'leftPanel', 'bottomPanel') as $panel) {
			if(!empty($modSettings['yaportal-'.$panel])) {
				$context['yaportal_'.$panel]['title'] 	= $panel;
				$context['yaportal_'.$panel]['content'] 	= '';
			}
		}

		$articles	= get_articles($start, $per_page);
		$total_articles = get_total_articles();

        $context['comments-enabled']    = isset($modSettings['yaportal-enablecomments']) ? $modSettings['yaportal-enablecomments'] : 0;
		$context['articles'] 		    = $articles;
		$context['page_index'] 		    = constructPageIndex($scripturl . '?action=home;start=%1$d', $start, $total_articles, $per_page, true);

        if (!Template_Layers::getInstance()->hasLayers(true) && !in_array('yaportal', Template_Layers::getInstance()->getLayers())) {
            Template_Layers::getInstance()->add('yaportal');
        }
	}

	public static function canFrontPage()
	{
		return true;
	}

	public static function frontPageHook(&$default_action)
	{
		// View the portal front page
		$file = CONTROLLERDIR . '/YAPortal.controller.php';
		$controller = 'YAPortal_Controller';
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
