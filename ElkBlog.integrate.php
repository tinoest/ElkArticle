<?php

class ElkBlog
{
	public static function integrate_action_frontpage(&$default_action)
	{
		$default_action = array (
			'file' 		=> CONTROLLERDIR . '/ElkBlog.controller.php',
			'controller' 	=> 'ElkBlog_Controller',
			'function' 	=> 'action_elkblog'
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

		loadLanguage('ElkBlog');
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

		loadLanguage('ElkBlog');

		$admin_areas['elkblog'] = array (
			'title' => $txt['elkblog-admin'],
			'permission' => array ('admin_forum'),
			'areas' => array (
				'blogconfig' => array (
					'label' => $txt['elkblog-adminConfiguration'],
					'file' => 'ElkBlogAdmin.controller.php',
					'controller' => 'ElkBlogAdmin_Controller',
					'function' => 'action_index',
					'icon' => 'transparent.png',
					'class' => 'admin_home_page',
					'permission' => array ( 'admin_forum' ),
					'subsections' => array (
						'editarticle' => array ( $txt['elkblog-addarticle'] ),
						'listarticle' => array ( $txt['elkblog-listarticle'] ),
					),
				),
			),
		);
	}
}
