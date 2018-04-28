<?php

function template_elkblog()
{
	global $context, $txt;
	
	echo '<div id="eb_view_articles">
		<h3 class="category_header">
			', $context['page_title'], '
		</h3>';

	foreach($context['articles'] as $article) {

		echo '<div class="eb_article">';
		echo '<h4>';
		echo $article['title'];
		echo '</h4>';
		echo '<div class="eb_article_body">';
		echo $article['body'];
		echo '</div>';
		
		echo sprintf('<div class="eb_article_details">Posted By: %s Category: %s Published %s </div>', $article['member'], $article['category'], $article['dt_published']);
		echo sprintf('<div class="eb_article_details">Views: %d Comments: %d </div>', $article['views'], $article['comments']);

		echo '</div>';
	}
	


	echo '</div>';

}
