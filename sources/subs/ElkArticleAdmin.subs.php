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

function get_articles_list($start, $items_per_page, $sort) 
{

	$db = database();

	require_once( SUBSDIR . '/ElkArticle.subs.php');

	$categories 	= get_article_categories();
	$request 	= $db->query('', '
		SELECT id, category_id, member_id, dt_created, dt_published, title,
			CASE WHEN status = 1 
				THEN \'Enabled\'
				ELSE \'Disabled\'
			END
			AS status
		FROM {db_prefix}articles
		ORDER BY '.$sort.'
		LIMIT '.$items_per_page.' OFFSET '.$start
	);

	$articles 	= array();
	while ($row = $db->fetch_assoc($request)) {
		$member	= $db->query('', '
			SELECT member_name
			FROM {db_prefix}members
			WHERE id_member = {int:member_id}',
			array ( 
				'member_id' => $row['member_id'],
			)
		);
		$row['member'] 		= $db->fetch_assoc($member)['member_name'];
		$row['category']	= $categories[$row['category_id']];
		$row['dt_created']	= htmlTime($row['dt_created']);
		$row['dt_published']	= htmlTime($row['dt_published']);
		$articles[] 		= $row; 
	}

	return $articles;

}


function insert_article($subject, $body, $category_id, $member_id) 
{

	$db = database();

	$db->insert('', 
	'{db_prefix}articles',
		array( 
			'member_id' 	=> 'int',
			'category_id'	=> 'int',
			'title'		=> 'string',
			'body'		=> 'string',
			'dt_published'	=> 'int',
		),
		array (
			$member_id,
			$category_id,
			$subject,
			$body,
			time(),
		),
		array('id')
	);
			
	$article_id 	= $db->insert_id('{db_prefix}articles', 'id');

	return $article_id;
}


function update_article( $subject, $body, $category_id, $article_id) 
{
	$db = database();
	
	$db->query('', '
	UPDATE {db_prefix}articles
	SET title = {string:title}, body = {string:body}, category_id = {int:category_id}
		WHERE id = {int:id}',
		array (
			'title' 	=> $subject,
			'body'		=> $body,
			'category_id'	=> $category_id,
			'id'		=> $article_id,
		)
	);
}

function delete_article($id)
{

	$db = database();
	
	$db->query('', '
		DELETE FROM {db_prefix}articles
		WHERE id = {int:id}',
		array (
			'id'		=> $id,
		)
	);
}

function insert_category($name, $desc)
{

	$db = database();
	
	$db->insert('', 
		'{db_prefix}article_categories',
		array( 
			'name' 		=> 'string',
			'description' 	=> 'string',
			'status'	=> 'int',
		),
		array (
			$name,
			$desc,
			1,
		),
		array('id')
	);
}

function update_category( $category_id, $category_name, $category_desc) 
{
	$db = database();
	
	$db->query('', '
	UPDATE {db_prefix}article_categories
	SET name = {string:category_name} ,
	description = {string:category_desc}
	WHERE id = {int:category_id}',
		array (
			'category_name' => $category_name,
			'category_desc' => $category_desc,
			'category_id'	=> $category_id,
		)
	);
}

function delete_category($id)
{

	$db = database();
	
	$db->query('', '
		DELETE FROM {db_prefix}article_categories
		WHERE id = {int:id}',
		array (
			'id'		=> $id,
		)
	);
}
