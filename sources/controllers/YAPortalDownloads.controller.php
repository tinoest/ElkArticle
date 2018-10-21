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

class YAPortalDownloads_Controller extends Action_Controller
{
	public function action_index()
	{
		require_once(SUBSDIR . '/Action.class.php');
		// Where do you want to go today?
		$subActions = array(
			'index'		=> array($this, 'action_yaportal_index'),
			'view' 	    => array($this, 'action_yaportal_download'),
		);

		// We like action, so lets get ready for some
		$action = new Action('');
		// Get the subAction, or just go to action_index
		$subAction = $action->initialize($subActions, 'index');

		// Finally go to where we want to go
		$action->dispatch($subAction);
	}

	public function action_yaportal_index()
	{
		global $context, $scripturl, $modSettings;

		require_once(SUBSDIR . '/YAPortalDownloads.subs.php');

		loadCSSFile('yaportal.css');

		$context['page_title']	= $context['forum_name'];
		$context['sub_template']= 'yaportal_download_index';

		// Set up for pagination
		$start 		            = !empty($_REQUEST['start']) ? (int) $_REQUEST['start'] : 0;
		$per_page               = 10;

		$categories             = get_download_categories($start, $per_page);
		$total_categories       = get_total_categories();

        foreach($categories as $id => $name) {
            $download_categories[] = get_category_download( $id );
        }

		$context['downloads']   = $download_categories;
		$context['page_index']  = constructPageIndex($scripturl . '?action=download;start=%1$d', $start, $total_categories, $per_page, true);

		loadTemplate('YAPortalDownloads');
	}

	public function action_yaportal_download()
	{
		global $context, $scripturl, $txt, $modSettings, $boardurl;

		require_once(SUBSDIR . '/YAPortalDownloads.subs.php');

        loadLanguage('YAPortal');
		loadCSSFile('yaportal.css');

		$context['page_title']		= $context['forum_name'];
		$context['sub_template'] 	= 'yaportal_download';

        $download_id 	    = !empty($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        $download_name 	    = !empty($_REQUEST['name']) ? (string) $_REQUEST['name'] : null;

		// Set up for pagination
		$start 	            = !empty($_REQUEST['start']) ? (int) $_REQUEST['start'] : 0;
		$per_page           = 10;
        $downloads          = array();
        $total_downloads    = array();

        if( !empty($download_id) ) {
		    $downloads	                    = get_downloads($start, $per_page, $download_id);
		    $total_downloads                = get_total_downloads($download_id);
        }
        else if( !is_null($download_name) ) {
		    $downloads	                    = get_downloads($start, $per_page, $download_name);
		    $total_downloads                = get_total_downloads($download_id);
        }

		$context['comments-enabled'] 	    = $modSettings['yaportal-enablecomments'];
       
        foreach($downloads as $id => $download) { 
            $downloads[$id]['download_link_src']        = $boardurl . '/yaportal/downloads/' . $download['download_link'];
        }

		$context['downloads'] 		        = $downloads;
		if(!empty($download_id)) {
		  $context['page_index'] 		    = constructPageIndex($scripturl . '?action=download;sa=download;id='.$download_id.';start=%1$d', $start, $total_downloads, $per_page, true);
		}
		else {
		  $context['page_index'] 		    = constructPageIndex($scripturl . '?action=download;sa=download;name='.$download_name.';start=%1$d', $start, $total_downloads, $per_page, true);
		}

        // Build the breadcrumbs
        $context['linktree'] = array_merge($context['linktree'], array(
            array(
                'url'   => $scripturl . '?download/',
                'name'  => $txt['yaportal-downloads'],
            ),
        ));

		loadTemplate('YAPortalDownloads');
	}
}
