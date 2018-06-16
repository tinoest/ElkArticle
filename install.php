<?php

global $db_prefix, $db_package_log;

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('ELK'))
{
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('ELK'))
{
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as ElkArte\'s index.php.');
}

$db 		= database();
$db_table 	= db_table();

$tables = array(
	'articles' => array(
		'columns' => array(
			array('name' => 'id', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'category_id', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'member_id', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'member_name', 'type' => 'varchar', 'size' => 80, 'default' => ''),
			array('name' => 'dt_created', 'type' => 'int', 'size' => 10, 'default' => 0),
			array('name' => 'dt_published', 'type' => 'int', 'size' => 10, 'default' => 0),
			array('name' => 'title', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'body', 'type' => 'text'),
			array('name' => 'type', 'type' => 'varchar', 'size' => 40, 'default' => ''),
			array('name' => 'date', 'type' => 'int', 'size' => 10, 'default' => 0, 'unsigned' => true),
			array('name' => 'permissions', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'styles', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'views', 'type' => 'int', 'size' => 10, 'default' => 0, 'unsigned' => true),
			array('name' => 'comments', 'type' => 'int', 'size' => 10, 'default' => 0, 'unsigned' => true),
			array('name' => 'status', 'type' => 'tinyint', 'size' => 4, 'default' => 1),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id')),
		),
	),
	'article_categories' => array(
		'columns' => array(
			array('name' => 'id', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'name', 'type' => 'varchar', 'size' => 255, 'default' => ''),
			array('name' => 'description', 'type' => 'text'),
			array('name' => 'permissions', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'articles', 'type' => 'int', 'size' => 10, 'default' => 0, 'unsigned' => true),
			array('name' => 'status', 'type' => 'tinyint', 'size' => 4, 'default' => 1),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id')),
		),
	),
	'article_comments' => array(
		'columns' => array(
			array('name' => 'id', 'type' => 'mediumint', 'size' => 8, 'auto' => true, 'unsigned' => true),
			array('name' => 'article_id', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'member_id', 'type' => 'mediumint', 'size' => 8, 'default' => 0, 'unsigned' => true),
			array('name' => 'member_name', 'type' => 'varchar', 'size' => 80, 'default' => ''),
			array('name' => 'body', 'type' => 'text'),
			array('name' => 'dt_created', 'type' => 'int', 'size' => 10, 'default' => 0),
		),
		'indexes' => array(
			array('type' => 'primary', 'columns' => array('id')),
		),
	),
);

foreach ($tables as $table => $data)
{
	$db_table->db_create_table('{db_prefix}' . $table, $data['columns'], $data['indexes'], array(), 'ignore');
}

// Add a default category if none exist
$request = $db->query('', 'SELECT COUNT(id) FROM {db_prefix}article_categories LIMIT 1');
if($db->num_rows($request) == 0) {
	$db->insert('ignore',
		'{db_prefix}article_categories',
		array (
			'name' 		=> 'string',
			'description' 	=> 'string',
			'status'	=> 'int'
		),
		array (
			array ( 'Default Category', 'Default Category', '1')
		),
		array ()
	);
}
$db->free_result($request);

// Update the mod settings
update_modSettings();

function update_modSettings() 
{
	global $modSettings;

	$mod_settings = array(
		'front_page' 			=> 'ElkArticle_Controller',
		'elkarticle-frontpage' 		=> 1,
		'elkarticle-topPanel' 		=> 0,
		'elkarticle-rightPanel' 	=> 0,
		'elkarticle-leftPanel' 		=> 0,
		'elkarticle-bottomPanel' 	=> 0,
		'elkarticle-item-limit' 	=> 0,
		'elkarticle-enablecomments' 	=> 0,
	);

	foreach ($mod_settings as $new_setting => $new_value) {
		if (!array_key_exists($new_setting, $modSettings)) {
			updateSettings(array($new_setting => $new_value));
		}
	}
}

?>
