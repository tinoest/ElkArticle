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

class YAPortal
{
	public static function integrate_pre_load()
	{
		global $boardurl;
		$original = $_SERVER['QUERY_STRING'];

        $paths = array (
			'~^article/$~'							    => 'action=article',
			'~^article/([0-9]+)/$~'					    => 'action=article&sa=article&id=%1$s',
			'~^article/([A-Za-z0-9]+)/$~'			    => 'action=article&sa=article&name=%1$s',
			'~^gallery/$~'							    => 'action=gallery',
			'~^gallery/([0-9]+)/$~'					    => 'action=gallery&sa=gallery&id=%1$s',
			'~^gallery/([A-Za-z0-9-.]+)/$~'			    => 'action=gallery&sa=gallery&name=%1$s',
			'~^gallery/image/([0-9]+)/$~'		    	=> 'action=gallery&sa=image&id=%1$s',
			'~^gallery/image/([A-Za-z0-9-i.]+)/$~'      => 'action=gallery&sa=image&name=%1$s',
			'~^gallery/rawimage/([0-9]+)/$~'		    => 'action=gallery&sa=rawimage&id=%1$s',
			'~^gallery/rawimage/([A-Za-z0-9-.]+)/$~'    => 'action=gallery&sa=rawimage&name=%1$s',
		);

		foreach ($paths as $route => $destination) {
			if (preg_match($route, $_SERVER['QUERY_STRING'], $matches)) {
				// Trailing / ?
				if (substr($_SERVER['QUERY_STRING'], -1) !== '/') {
					header('Location: ' . $boardurl . '/index.php?' . $_SERVER['QUERY_STRING'] . '/', true, 301);
					exit;
				}

				if (count($matches) > 1) {
					array_shift($matches);
					$_SERVER['QUERY_STRING'] = vsprintf($destination, $matches);
				}
				else {
					$_SERVER['QUERY_STRING'] = $destination;
				}
			}
		}

		// If we've matched, we need to rewrite the original requested URI too.
		if ($original != $_SERVER['QUERY_STRING']) {
			$_SERVER['REQUEST_URI'] = $boardurl . '/index.php?' . $_SERVER['QUERY_STRING'];
			// clean the request to reset the _req instance
			cleanRequest();
		}
	}

	public static function integrate_action_frontpage(&$default_action)
	{
		global $modSettings;

		if(!empty($modSettings['yaportal-frontpage'])) {
			$default_action = array (
				'file' 		=> CONTROLLERDIR . '/YAPortal.controller.php',
				'controller'=> 'YAPortal_Controller',
				'function' 	=> 'action_yaportal'
			);
		}
	}

	public static function integrate_actions(&$actionArray, &$adminActions)
	{
		global $modSettings;

		if(!empty($modSettings['yaportal-frontpage'])) {
			$actionArray['forum'] = array (
				'BoardIndex.controller.php',
				'BoardIndex_Controller',
				'action_boardindex'
			);
		}

        if(!empty($modSettings['yaportal-article-menu-item'])) {
            $actionArray['article'] = array (
				'YAPortal.controller.php',
				'YAPortal_Controller',
				'action_index'
			);
        }

        if(!empty($modSettings['yaportal-gallery-menu-item'])) {
            $actionArray['gallery'] = array (
				'YAPortalGallery.controller.php',
				'YAPortalGallery_Controller',
				'action_index'
			);
        }
	}

	public static function integrate_current_action(&$current_action)
	{
		global $modSettings;

		if(!empty($modSettings['yaportal-frontpage']) && ($current_action === 'home')) {
			if (empty($_REQUEST['action'])) {
				$current_action = 'base';
			}
		}
	}

	public static function integrate_menu_buttons(&$buttons, &$menu_count)
	{
		global $txt, $boardurl, $scripturl, $modSettings, $context;

		if(!empty($modSettings['yaportal-frontpage'])) {
			loadLanguage('YAPortal');
			$buttons = elk_array_insert($buttons, 'home', array (
				'base' => array(
					'title' 	    => $txt['home_btn'],
					'href' 		    => $boardurl,
					'data-icon'     => 'i-home',
					'show'          => true,
					'action_hook' 	=> true,
				),
			));

			// Change the home icon to something else and rewrite the standard action
			$buttons['home']['data-icon'] = 'i-users';
			$buttons['home']['href']      = $scripturl . '?action=forum';
		}

        // Show the Gallery Option Tab
        if(!empty($modSettings['yaportal-gallery-menu-item'])) {
			loadLanguage('YAPortal');

            $context['html_headers'] .= '
            <style>
                .i-gallery::before {
                    content: url("data:image/svg+xml,%3Csvg version=\'1.1\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 32 32\'%3E%3Ctitle%3Ecamera%3C/title%3E%3Cpath d=\'M9.5 19c0 3.59 2.91 6.5 6.5 6.5s6.5-2.91 6.5-6.5-2.91-6.5-6.5-6.5-6.5 2.91-6.5 6.5zM30 8h-7c-0.5-2-1-4-3-4h-8c-2 0-2.5 2-3 4h-7c-1.1 0-2 0.9-2 2v18c0 1.1 0.9 2 2 2h28c1.1 0 2-0.9 2-2v-18c0-1.1-0.9-2-2-2zM16 27.875c-4.902 0-8.875-3.973-8.875-8.875s3.973-8.875 8.875-8.875c4.902 0 8.875 3.973 8.875 8.875s-3.973 8.875-8.875 8.875zM30 14h-4v-2h4v2z\'%3E%3C/path%3E%3C/svg%3E%0A");
                }
            </style>';

			$buttons = elk_array_insert($buttons, 'home', array (
				'gallery' => array(
					'title' 	    => $txt['gallery_btn'],
					'href' 		    => $scripturl . '?action=gallery',
					'data-icon'     => 'i-gallery',
					'show'          => true,
					'action_hook' 	=> true,
	            ),
	        ), 'after');
        }

        // Show the article option tab
        if(!empty($modSettings['yaportal-article-menu-item'])) {
			loadLanguage('YAPortal');

            $context['html_headers'] .= '
            <style>
                .i-newspaper::before {
                    content: url("data:image/svg+xml,%3C!-- Generated by IcoMoon.io --%3E%3Csvg version=\'1.1\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 32 32\'%3E%3Ctitle%3Enewspaper%3C/title%3E%3Cpath d=\'M28 8v-4h-28v22c0 1.105 0.895 2 2 2h27c1.657 0 3-1.343 3-3v-17h-4zM26 26h-24v-20h24v20zM4 10h20v2h-20zM16 14h8v2h-8zM16 18h8v2h-8zM16 22h6v2h-6zM4 14h10v10h-10z\'%3E%3C/path%3E%3C/svg%3E%0A");
                }
            </style>';			
            
            $buttons = elk_array_insert($buttons, 'home', array (
				'article' => array(
					'title' 	    => $txt['article_btn'],
					'href' 		    => $scripturl . '?action=article',
					'data-icon'     => 'i-newspaper',
					'show'          => true,
					'action_hook' 	=> true,
	            ),
	        ), 'after');
        }
	}

	public static function integrate_admin_areas(&$admin_areas)
	{
		global $txt;

		loadLanguage('YAPortal');

		$admin_areas['yaportal'] = array (
			'title' => $txt['yaportal-admin'],
			'permission' => array ('admin_forum' , 'yaportal_admin'),
			'areas' => array (
				'yaportalconfig' => array (
					'label'       => $txt['yaportal-adminConfigurationMain'],
					'file'        => 'YAPortalAdminMain.controller.php',
					'controller'  => 'YAPortalAdminMain_Controller',
					'function'    => 'action_index',
					'icon'        => 'transparent.png',
					'class'       => 'admin_home_page',
					'permission'  => array ( 'admin_forum', 'yaportal_manage_settings' ),
					'subsections' => array (
						'listsettings'	=> array ( $txt['yaportal-settings'] ),
					),
				),
				'yaportalarticles' => array (
					'label'       => $txt['yaportal-adminConfigurationArticles'],
					'file'        => 'YAPortalAdminArticles.controller.php',
					'controller'  => 'YAPortalAdminArticles_Controller',
					'function'    => 'action_index',
					'icon'        => 'transparent.png',
					'class'       => 'admin_home_page',
					'permission'  => array ( 'admin_forum', 'yaportal_manage_articles' ),
					'subsections' => array (
						'listarticle' 	=> array ( $txt['yaportal-listarticle'] ),
						'listcategory' 	=> array ( $txt['yaportal-listcategory'] ),
					),
				),
				'yaportalblocks' => array (
					'label'       => $txt['yaportal-adminConfigurationBlocks'],
					'file'        => 'YAPortalAdminBlocks.controller.php',
					'controller'  => 'YAPortalAdminBlocks_Controller',
					'function'    => 'action_index',
					'icon'        => 'transparent.png',
					'class'       => 'admin_home_page',
					'permission'  => array ( 'admin_forum', 'yaportal_manage_blocks' ),
					'subsections' => array (
						'listblock'	    => array ( $txt['yaportal-listblocks'] ),
					),
				),
				'yaportalgallery' => array (
					'label'       => $txt['yaportal-adminConfigurationGallery'],
					'file'        => 'YAPortalAdminGallery.controller.php',
					'controller'  => 'YAPortalAdminGallery_Controller',
					'function'    => 'action_index',
					'icon'        => 'transparent.png',
					'class'       => 'admin_home_page',
					'permission'  => array ( 'admin_forum', 'yaportal_manage_gallery' ),
					'subsections' => array (
						'listgallery'	    => array ( $txt['yaportal-listgallery'] ),
						'listcategory' 	    => array ( $txt['yaportal-listcategory'] ),
					),
				),
			),
		);
	}

    public static function integrate_whos_online($actions)
    {
        global $scripturl, $txt;

		loadLanguage('YAPortal');

        if(array_key_exists('action', $actions) && $actions['action'] == 'article') {
            require_once(SUBSDIR . '/YAPortal.subs.php');
            if(array_key_exists('id', $actions) && !empty($actions['id'])) {
                $articleData = get_article((int)$actions['id']);
            }
            else if(array_key_exists('name', $actions) && !empty($actions['name'])) {
                $articleData = get_article($actions['name']);
            }
            if(!empty($articleData)) {
                return sprintf($txt['yaportal_whos_online_article_name'], $articleData['id'], censorText($articleData['title']), $scripturl);
            }
            else {
                return $txt['yaportal_whos_online_article_list'];
            }
        }
        else if(array_key_exists('action', $actions) && $actions['action'] == 'gallery') {
            require_once(SUBSDIR . '/YAPortalGallery.subs.php');
            if(array_key_exists('id', $actions) && !empty($actions['id'])) {
                $articleData = get_gallery((int)$actions['id']);
            }
            else if(array_key_exists('name', $actions) && !empty($actions['name'])) {
                $articleData = get_gallery($actions['name']);
            }
            if(!empty($articleData)) {
                return sprintf($txt['yaportal_whos_online_gallery_name'], $articleData['id'], censorText($articleData['title']), $scripturl);
            }
            else {
                return $txt['yaportal_whos_online_gallery_list'];
            }
        }
    }

    public static function integrate_load_permissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions)
    {
        $permissionList['membergroup'] = array_merge($permissionList['membergroup'], array(
                'yaportal_admin'            => array(false, 'yaportal', 'yaportal'),
                'yaportal_manage_settings'  => array(false, 'yaportal', 'yaportal'),
                'yaportal_manage_blocks'    => array(false, 'yaportal', 'yaportal'),
                'yaportal_manage_articles'  => array(false, 'yaportal', 'yaportal'),
                'yaportal_manage_gallery'   => array(false, 'yaportal', 'yaportal'),
            )
        );
        $permissionGroups['membergroup'][]  = 'yaportal';
        $leftPermissionGroups[]             = 'yaportal';
    }

}
