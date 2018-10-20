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

function get_downloads_list($start, $items_per_page, $sort)
{

	$db = database();

	require_once( SUBSDIR . '/YAPortalDownloads.subs.php');

	$categories 	= get_download_categories();
	$request 	    = $db->query('', '
		SELECT id, category_id, member_id, dt_created, dt_published, title,
			CASE WHEN status = 1
				THEN \'Enabled\'
				ELSE \'Disabled\'
			END
			AS status
		FROM {db_prefix}downloads
		ORDER BY '.$sort.'
		LIMIT '.$items_per_page.' OFFSET '.$start
	);

	$downloads 	= array();
	while ($row = $db->fetch_assoc($request)) {
		$member	= $db->query('', '
			SELECT member_name
			FROM {db_prefix}members
			WHERE id_member = {int:member_id}',
			array (
				'member_id' => $row['member_id'],
			)
		);
		$row['member'] 		    = $db->fetch_assoc($member)['member_name'];
		if(array_key_exists($row['category_id'], $categories)) {
			$row['category']	= $categories[$row['category_id']];
		}
		else {
			$row['category']	= 'Category Disabled';
		}
		$row['dt_created']	    = htmlTime($row['dt_created']);
		$row['dt_published']	= htmlTime($row['dt_published']);
		$downloads[] 		    = $row;
	}

	return $downloads;

}


function insert_download($subject, $body, $category_id, $member_id, $download_link, $status)
{

	$db = database();

	$db->insert('',
	'{db_prefix}downloads',
		array(
			'member_id' 	=> 'int',
			'category_id'	=> 'int',
			'title'		    => 'string',
			'body'		    => 'string',
			'download_link'	=> 'string',
			'dt_published'	=> 'int',
			'status'	    => 'int',
		),
		array (
			$member_id,
			$category_id,
			$subject,
			$body,
			$download_link,
			time(),
			$status,
		),
		array('id')
	);

	$download_id 	= $db->insert_id('{db_prefix}downloads', 'id');

	return $download_id;
}


function update_download( $subject, $body, $category_id, $download_id, $download_link, $status)
{
	$db = database();

	if(is_null($body) && is_null($download_link)) {
		$db->query('', '
		UPDATE {db_prefix}downloads
		SET title = {string:title}, category_id = {int:category_id}, status = {int:status}
			WHERE id = {int:id}',
			array (
				'title' 	    => $subject,
				'category_id'	=> $category_id,
				'status'	    => $status,
				'id'		    => $download_id,
			)
		);
	}
	else if(is_null($body)) {
		$db->query('', '
		UPDATE {db_prefix}downloads
		SET title = {string:title}, category_id = {int:category_id}, status = {int:status}, download_link = {string:download_link}
			WHERE id = {int:id}',
			array (
				'title' 	    => $subject,
				'category_id'	=> $category_id,
				'status'	    => $status,
                'download_link'    => $download_link,
				'id'		    => $download_id,
			)
		);
	}
	else if(is_null($download_link)) {
		$db->query('', '
		UPDATE {db_prefix}downloads
		SET title = {string:title}, category_id = {int:category_id}, status = {int:status}, body = {string:body}
			WHERE id = {int:id}',
			array (
				'title' 	    => $subject,
				'category_id'	=> $category_id,
				'status'	    => $status,
                'body'          => $body,
				'id'		    => $download_id,
			)
		);
	}
	else {
		$db->query('', '
		UPDATE {db_prefix}downloads
		SET title = {string:title}, body = {string:body}, category_id = {int:category_id}, status = {int:status}, download_link = {string:download_link}
			WHERE id = {int:id}',
			array (
				'title' 	    => $subject,
				'body'		    => $body,
				'download_link'	=> $download_link,
				'category_id'	=> $category_id,
				'status'	    => $status,
				'id'		    => $download_id,
			)
		);
	}
}

function delete_download($id)
{

	$db = database();

	$db->query('', '
		DELETE FROM {db_prefix}downloads
		WHERE id = {int:id}',
		array (
			'id'		=> $id,
		)
	);
}

function insert_category($name, $desc, $status)
{

	$db = database();

	$db->insert('',
		'{db_prefix}download_categories',
		array(
			'name' 		=> 'string',
			'description' 	=> 'string',
			'status'	=> 'int',
		),
		array (
			$name,
			$desc,
			$status,
		),
		array('id')
	);
}

function update_category( $category_id, $category_name, $category_desc, $category_enabled)
{
	$db = database();

	$db->query('', '
	UPDATE {db_prefix}download_categories
	SET name = {string:category_name} ,
	description = {string:category_desc},
	status = {int:category_enabled}
	WHERE id = {int:category_id}',
		array (
			'category_name' 	=> $category_name,
			'category_desc' 	=> $category_desc,
			'category_enabled'	=> $category_enabled,
			'category_id'		=> $category_id,
		)
	);
}

function delete_category($id)
{

	$db = database();

	$db->query('', '
		DELETE FROM {db_prefix}download_categories
		WHERE id = {int:id}',
		array (
			'id'		=> $id,
		)
	);
}


