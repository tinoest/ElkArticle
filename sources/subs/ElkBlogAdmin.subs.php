<?php


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

