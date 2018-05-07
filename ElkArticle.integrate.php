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

class ElkArticle
{
	public static function integrate_action_frontpage(&$default_action)
	{
		$default_action = array (
			'file' 		=> CONTROLLERDIR . '/ElkArticle.controller.php',
			'controller' 	=> 'ElkArticle_Controller',
			'function' 	=> 'action_elkarticle'
		);
	}

	public static function integrate_actions(&$actionArray, &$adminActions)
	{
		$actionArray['forum'] = array (
			'BoardIndex.controller.php',
			'BoardIndex_Controller',
			'action_boardindex'
		);
	}

	public static function integrate_current_action(&$current_action)
	{
		if ($current_action === 'home') {
			if (empty($_REQUEST['action'])) {
				$current_action = 'base';
			}
		}
	}

	public static function integrate_menu_buttons(&$buttons, &$menu_count)
	{
		global $txt, $boardurl, $scripturl;

		loadLanguage('ElkArticle');
		$buttons = elk_array_insert($buttons, 'home', array (
			'base' => array(
				'title' 	=> $txt['home_btn'],
				'href' 		=> $boardurl,
				'data-icon' 	=> 'i-home',
				'show' 		=> true,
				'action_hook' 	=> true,
			),
		));

		// Change the home icon to something else and rewrite the standard action
		$buttons['home']['data-icon'] = 'i-users';
		$buttons['home']['href'] = $scripturl . '?action=forum';
	}

	public static function integrate_admin_areas(&$admin_areas)
	{
		global $txt;

		loadLanguage('ElkArticle');

		$admin_areas['elkarticle'] = array (
			'title' => $txt['elkarticle-admin'],
			'permission' => array ('admin_forum'),
			'areas' => array (
				'articleconfig' => array (
					'label' => $txt['elkarticle-adminConfiguration'],
					'file' => 'ElkArticleAdmin.controller.php',
					'controller' => 'ElkArticleAdmin_Controller',
					'function' => 'action_index',
					'icon' => 'transparent.png',
					'class' => 'admin_home_page',
					'permission' => array ( 'admin_forum' ),
					'subsections' => array (
						'editarticle' 	=> array ( $txt['elkarticle-addarticle'] ),
						'listarticle' 	=> array ( $txt['elkarticle-listarticle'] ),
						'listcategory' 	=> array ( $txt['elkarticle-listcategory'] ),
					),
				),
			),
		);
	}
}
