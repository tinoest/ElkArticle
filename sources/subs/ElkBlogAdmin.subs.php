<?php

function get_blog_articles_list() 
{

	$db = database();

	require_once( SUBSDIR . '/ElkBlog.subs.php');

	$categories 	= get_blog_categories();
	$request 	= $db->query('', '
		SELECT id, category_id, member_id, dt_created, dt_published, title,
			CASE WHEN status = 1 
				THEN \'Enabled\'
				ELSE \'Disabled\'
			END
			AS status
		FROM {db_prefix}blog_articles
		ORDER BY id DESC'
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


function insert_blog_article($subject, $body, $category_id, $member_id) 
{

	$db = database();

	$db->insert('', 
	'{db_prefix}blog_articles',
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
			
	$blog_id 	= $db->insert_id('{db_prefix}blog_articles', 'id');

	return $blog_id;
}


function update_blog_article( $subject, $body, $category_id, $article_id) 
{
	$db = database();
	
	$db->query('', '
	UPDATE {db_prefix}blog_articles
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

function delete_blog_article($id)
{

	$db = database();
	
	$db->query('', '
		DELETE FROM {db_prefix}blog_articles
		WHERE id = {int:id}
		LIMIT 1',
		array (
			'id'		=> $id,
		)
	);
}
