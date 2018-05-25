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

function get_articles( $start, $per_page)
{
	$db 		= database();
	
	$categories	= get_article_categories();
	$request  	= $db->query('', '
		SELECT id, category_id, member_id, dt_published, title, body, views, comments
		FROM {db_prefix}articles
		WHERE status = 1
		ORDER BY id DESC
		LIMIT '.$per_page.' OFFSET '.$start
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
		$articles[] 		= $row; 
	}

	$db->free_result($request);

	return $articles;	
}

function get_article( $id )
{
	$db 		= database();
	
	$categories	= get_article_categories();
	$request  	= $db->query('', '
		SELECT id, category_id, member_id, dt_published, title, body, views, comments
		FROM {db_prefix}articles
		WHERE id = {int:id}',
		array (
			'id' => $id,
		)
	);

	$article 	= array();
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
		$article 		= $row; 
	}

	$db->free_result($request);

	return $article;	
}



function get_total_articles()
{
	$total_articles	= 0;

	$db 		= database();
	$request 	= $db->query('', '
		SELECT COUNT(id) as num_articles
		FROM {db_prefix}articles
		WHERE status = 1'
	);
	
	$total_articles = $db->fetch_assoc($request)['num_articles']; 

	$db->free_result($request);

	return $total_articles;
}

function get_article_categories()
{
	$categories	= array();
	$db 		= database();
	$request 	= $db->query('', '
		SELECT id, name
		FROM {db_prefix}article_categories
		WHERE status = 1'
	);
	
	while ($row = $db->fetch_assoc($request)) {
		$categories[$row['id']] = $row['name'];
	}

	$db->free_result($request);

	return $categories;
}

function get_category($id)
{
	$category	= array();
	$db 		= database();
	$request	= $db->query('', '
		SELECT id, name, description, articles, status AS enabled,
			CASE WHEN status = 1 
				THEN \'Enabled\'
				ELSE \'Disabled\'
			END
			AS status
		FROM {db_prefix}article_categories
		WHERE id = {int:id}',
		array (
			'id' => $id,
		)
	);
	
	while ($row = $db->fetch_assoc($request)) {
		$category = $row;
	}

	$db->free_result($request);

	return $category;
}

function get_category_list($start, $items_per_page, $sort)
{
	$categories	= array();
	$db 		= database();
	$request	= $db->query('', '
		SELECT id, name, description, articles, status AS enabled,
			CASE WHEN status = 1 
				THEN \'Enabled\'
				ELSE \'Disabled\'
			END
			AS status
		FROM {db_prefix}article_categories
		ORDER BY '.$sort.'
		LIMIT '.$items_per_page.' OFFSET '.$start
	);
	
	while ($row = $db->fetch_assoc($request)) {
		$categories[] = $row;
	}

	$db->free_result($request);

	return $categories;
}

function get_total_categories()
{
	$db 		= database();
	$request	= $db->query('', '
		SELECT COUNT(id) AS count
		FROM {db_prefix}article_categories
		WHERE status = 1'
	);
	
	$count 		= $db->fetch_assoc($request)['count'];

	$db->free_result($request);

	return $count;
}

function update_article_views($article_id) 
{
	$db = database();
	
	$db->query('', '
	UPDATE {db_prefix}articles
	SET views = views + 1 
		WHERE id = {int:id}',
		array (
			'id'		=> $article_id,
		)
	);
}
