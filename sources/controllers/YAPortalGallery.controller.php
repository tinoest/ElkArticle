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

class YAPortalGallery_Controller extends Action_Controller
{
	public function action_index()
	{
		require_once(SUBSDIR . '/Action.class.php');
		// Where do you want to go today?
		$subActions = array(
			'index'		=> array($this, 'action_yaportal_index'),
			'gallery' 	=> array($this, 'action_yaportal_gallery'),
			'image' 	=> array($this, 'action_yaportal_image'),
		);

		// We like action, so lets get ready for some
		$action = new Action('');
		// Get the subAction, or just go to action_sportal_index
		$subAction = $action->initialize($subActions, 'index');

		// Finally go to where we want to go
		$action->dispatch($subAction);
	}


	public function action_yaportal_index()
	{
		global $context, $scripturl, $modSettings;

		require_once(SUBSDIR . '/YAPortalGallery.subs.php');

		loadCSSFile('yaportal.css');

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

		$galleries	                    = get_galleries($start, $per_page);
		$total_galleries                = get_total_galleries();

		$context['comments-enabled'] 	= $modSettings['yaportal-enablecomments'];
		$context['galleries'] 		    = $galleries;
		$context['page_index'] 		    = constructPageIndex($scripturl . '?action=home;start=%1$d', $start, $total_galleries, $per_page, true);

		loadTemplate('YAPortalGallery');
	}

    public function action_yaportal_gallery()
	{

		global $context, $scripturl, $txt, $modSettings;
		loadLanguage('YAPortal');
		loadCSSFile('yaportal.css');

		require_once(SUBSDIR . '/YAPortalGallery.subs.php');

		$context['page_title']		= $context['forum_name'];
		$context['sub_template'] 	= 'yaportal';

        $gallery_id 			    = !empty($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        $gallery_name 			    = !empty($_REQUEST['name']) ? (string) $_REQUEST['name'] : null;
        $gallery                    = array();
        if(!empty($gallery_id)) {
		    $gallery			    = get_gallery($gallery_id);
        }
        else if ( !is_null($gallery_name) ) {
		    $gallery			    = get_gallery($gallery_name);
        }

		if(is_array($gallery) && !empty($gallery)) {
			update_gallery_views($gallery_id);
			$context['gallery'] 	= $gallery;
		}
		else {
			$context['gallery_error']   = $txt['yaportal-not-found'];
		}
		$context['comments-enabled']    = $modSettings['yaportal-enablecomments'];

		loadTemplate('YAPortalGallery');
	}

    // Used to just show the image and no template
    public function action_yaportal_image()
    {
        global $context;

		require_once(SUBSDIR . '/YAPortalGallery.subs.php');

        $gallery_id 			    = !empty($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        $gallery_name 			    = !empty($_REQUEST['name']) ? (string) $_REQUEST['name'] : null;
        $gallery                    = array();
        if(!empty($gallery_id)) {
		    $gallery			    = get_gallery($gallery_id);
        }
        else if ( !is_null($gallery_name) ) {
		    $gallery			    = get_gallery($gallery_name);
        }

        if(is_array( $gallery ) ) {
            $fileName = BOARDDIR . '/yaportal/img/'. $gallery['image_name'];

            if(file_exists( $fileName ) ) {
                $context['image_mime_type'] = image_type_to_mime_type(exif_imagetype($fileName));
                $context['image_content']   = file_get_contents ( $fileName );
            }

            $context['page_title']		= $context['forum_name'];

            // Clean out the template layers
            $template_layers = Template_Layers::instance();
            $template_layers->removeAll();
        }
        else {
			$context['gallery_error']   = $txt['yaportal-not-found'];
        }

        $context['sub_template'] 	= 'yaportal_image';
		loadTemplate('YAPortalGallery');

    }

}