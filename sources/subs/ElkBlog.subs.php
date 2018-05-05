<?php

function get_blog_articles( $start, $per_page)
{
	$db 		= database();
	
	$categories	= get_blog_categories();
	$request  	= $db->query('', '
		SELECT category_id, member_id, dt_published, title, body, views, comments
		FROM {db_prefix}blog_articles
		WHERE status = 1
		ORDER BY id DESC
		LIMIT '.$start.', '.$per_page
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

function get_blog_article( $id )
{
	$db 		= database();
	
	$categories	= get_blog_categories();
	$request  	= $db->query('', '
		SELECT id, category_id, member_id, dt_published, title, body, views, comments
		FROM {db_prefix}blog_articles
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



function get_total_blog_articles()
{
	$total_articles	= 0;

	$db 		= database();
	$request 	= $db->query('', '
		SELECT COUNT(id) as num_articles
		FROM {db_prefix}blog_articles
		WHERE status = 1'
	);
	
	$total_articles = $db->fetch_assoc($request)['num_articles']; 

	$db->free_result($request);

	return $total_articles;
}

function get_blog_categories()
{
	$categories	= array();
	$db 		= database();
	$request 	= $db->query('', '
		SELECT id, name
		FROM {db_prefix}blog_categories
		WHERE status = 1'
	);
	
	while ($row = $db->fetch_assoc($request)) {
		$categories[$row['id']] = $row['name'];
	}

	$db->free_result($request);

	return $categories;
}
