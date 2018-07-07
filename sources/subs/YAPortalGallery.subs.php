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

function get_galleries( $start, $per_page)
{
	$db 		= database();
	
	$categories	= get_gallery_categories();
	$request  	= $db->query('', '
		SELECT id, category_id, member_id, dt_published, title, body, image_name, views, comments
		FROM {db_prefix}galleries
		WHERE status = 1
		ORDER BY id DESC
		LIMIT '.$per_page.' OFFSET '.$start
	);

	$galleries 	= array();
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
		if(array_key_exists($row['category_id'], $categories)) {
			$row['category']	= $categories[$row['category_id']];	
			$galleries[] 		= $row; 
		}
		else {
			unset($row);
		}
	}

	$db->free_result($request);

	return $galleries;	
}

function get_gallery( $id )
{
	$db 		= database();
	
	$categories	= get_gallery_categories();
	$request  	= $db->query('', '
		SELECT id, category_id, member_id, dt_published, title, body, image_name, views, comments, status
		FROM {db_prefix}galleries
		WHERE id = {int:id}',
		array (
			'id' => $id,
		)
	);

	$gallery 	= array();
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
		$gallery 		= $row; 
	}

	$db->free_result($request);

	return $gallery;	
}



function get_total_galleries()
{
	$total_galleries	= 0;

	$db 		= database();
	$request 	= $db->query('', '
		SELECT COUNT(id) as num_galleries
		FROM {db_prefix}galleries
		WHERE status = 1'
	);
	
	$total_galleries = $db->fetch_assoc($request)['num_galleries']; 

	$db->free_result($request);

	return $total_galleries;
}

function get_gallery_categories()
{
	$categories	= array();
	$db 		= database();
	$request 	= $db->query('', '
		SELECT id, name
		FROM {db_prefix}gallery_categories
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
		SELECT id, name, description, galleries, status AS enabled,
			CASE WHEN status = 1 
				THEN \'Enabled\'
				ELSE \'Disabled\'
			END
			AS status
		FROM {db_prefix}gallery_categories
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
		SELECT id, name, description, galleries, status AS enabled,
			CASE WHEN status = 1 
				THEN \'Enabled\'
				ELSE \'Disabled\'
			END
			AS status
		FROM {db_prefix}gallery_categories
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
		FROM {db_prefix}gallery_categories
		WHERE status = 1'
	);
	
	$count 		= $db->fetch_assoc($request)['count'];

	$db->free_result($request);

	return $count;
}

function update_gallery_views($gallery_id) 
{
	$db = database();
	
	$db->query('', '
	UPDATE {db_prefix}galleries
	SET views = views + 1 
		WHERE id = {int:id}',
		array (
			'id'		=> $gallery_id,
		)
	);
}
