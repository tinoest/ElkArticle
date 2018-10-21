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

function get_downloads( $start, $per_page, $search)
{
	$db 		= database();

	$categories	= get_download_categories();
	$request  	= $db->query('', '
		SELECT id, category_id, member_id, dt_published, title, body, download_link, views, comments
		FROM {db_prefix}downloads
		WHERE status = 1
        AND category_id = {int:search}
		ORDER BY id DESC
		LIMIT {int:per_page}  OFFSET {int:offset}',
        array ( 
            'search'    => $search,
            'per_page'  => $per_page,
            'offset'    => $start,
        )
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
		$row['member'] 		= $db->fetch_assoc($member)['member_name'];
		if(array_key_exists($row['category_id'], $categories)) {
			$row['category']	= $categories[$row['category_id']];
			$downloads[] 		= $row;
		}
		else {
			unset($row);
		}
	}

	$db->free_result($request);

	return $downloads;
}

function get_download( $search )
{
	$db 		= database();

	$categories	= get_download_categories();

    if( is_numeric($search) ) {
        $request  	= $db->query('', '
            SELECT id, category_id, member_id, dt_published, title, body, download_link, views, comments, status
            FROM {db_prefix}downloads
            WHERE id = {int:id}',
            array (
                'id' => $search,
            )
        );
    }
    else {
        $request  	= $db->query('', '
            SELECT id, category_id, member_id, dt_published, title, body, download_link, views, comments, status
            FROM {db_prefix}downloads
            WHERE title = {string:title}',
            array (
                'title' => $search,
            )
        );
    }

	$download 	= array();
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
		$download 		= $row;
	}

	$db->free_result($request);

	return $download;
}



function get_total_downloads( $search = null )
{
	$total_downloads	= 0;

    $db 		= database();
    if(is_null($search)) {
        $request 	= $db->query('', '
            SELECT COUNT(id) as num_downloads
            FROM {db_prefix}downloads'
        );
    }
    else {
        $request 	= $db->query('', '
            SELECT COUNT(id) as num_downloads
            FROM {db_prefix}downloads
            WHERE status = 1
            AND category_id = {int:search}',
            array (
                'search' => $search
            )
        );
    }

	$total_downloads = $db->fetch_assoc($request)['num_downloads'];

	$db->free_result($request);

	return $total_downloads;
}

function get_download_categories()
{
	$categories	= array();
	$db 		= database();
	$request 	= $db->query('', '
		SELECT id, name, description
		FROM {db_prefix}download_categories
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
		SELECT id, name, description, downloads, status AS enabled,
			CASE WHEN status = 1
				THEN \'Enabled\'
				ELSE \'Disabled\'
			END
			AS status
		FROM {db_prefix}download_categories
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
		SELECT id, name, description, downloads, status AS enabled,
			CASE WHEN status = 1
				THEN \'Enabled\'
				ELSE \'Disabled\'
			END
			AS status
		FROM {db_prefix}download_categories
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
		FROM {db_prefix}download_categories
		WHERE status = 1'
	);

	$count 		= $db->fetch_assoc($request)['count'];

	$db->free_result($request);

	return $count;
}

function get_category_download( $search )
{
	$db 		= database();

	$categories	= get_download_categories();

    if( is_numeric($search) ) {
        $request  	= $db->query('', '
            SELECT id, category_id, member_id, dt_published, title, body, download_link, views, comments, status
            FROM {db_prefix}downloads
            WHERE category_id = {int:id}
            AND status = 1
            LIMIT 1',
            array (
                'id' => $search,
            )
        );
    }
    
	$download 	= array();
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
		$row['category_id']	    = $row['category_id'];
		$row['category_name']	= $categories[$row['category_id']];
		$download 		        = $row;
	}

	$db->free_result($request);

	return $download;
}



function update_download_views($download_id)
{
	$db = database();

	$db->query('', '
	UPDATE {db_prefix}downloads
	SET views = views + 1
		WHERE id = {int:id}',
		array (
			'id'	=> $download_id,
		)
	);
}
