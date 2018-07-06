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
			'~^article/([0-9]+)/$~' => 'sa=article&article=%1$s',
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
				'controller' 	=> 'YAPortal_Controller',
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
		global $txt, $boardurl, $scripturl, $modSettings;

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
	}

	public static function integrate_admin_areas(&$admin_areas)
	{
		global $txt;

		loadLanguage('YAPortal');

		$admin_areas['yaportal'] = array (
			'title' => $txt['yaportal-admin'],
			'permission' => array ('admin_forum'),
			'areas' => array (
				'yaportalconfig' => array (
					'label'       => $txt['yaportal-adminConfiguration'],
					'file'        => 'YAPortalAdmin.controller.php',
					'controller'  => 'YAPortalAdmin_Controller',
					'function'    => 'action_index',
					'icon'        => 'transparent.png',
					'class'       => 'admin_home_page',
					'permission'  => array ( 'admin_forum' ),
					'subsections' => array (
						'listarticle' 	=> array ( $txt['yaportal-listarticle'] ),
						'listcategory' 	=> array ( $txt['yaportal-listcategory'] ),
						'listblock'	    => array ( $txt['yaportal-listblocks'] ),
						'listsettings'	=> array ( $txt['yaportal-settings'] ),
					),
				),
			),
		);
	}
}
