<?php

function template_elkblog()
{
	global $context, $txt;
	
	echo '<div id="eb_view_articles">';

	foreach($context['blog_articles'] as $article) {

		echo '<div class="eb_article" style="padding: 0.1em;">';
		echo '<h3 class="category_header">';
		echo $article['title'];
		echo sprintf('<span class="views_text"> (Views: %d, Comments: %d) </span>', $article['views'], $article['comments']);
		echo '</h3>';
		echo '<section>';
		echo '<article class="post_wrapper forumposts">';
		echo $article['body'];
		echo '<header class="keyinfo">';	
		echo sprintf('Posted By: %s<br />Category: %s<br />Published: %s', $article['member'], $article['category'], htmlTime($article['dt_published']));
		echo '</header>';
		echo '</article>';
		echo '</section>';
		echo '</div>';
	}
	


	echo '</div>';

}
