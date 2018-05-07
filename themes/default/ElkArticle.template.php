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

function template_elkarticle()
{
	global $context, $txt;
	
	echo '<div id="eb_view_articles">';

	foreach($context['articles'] as $article) {

		echo '<div class="eb_article" style="padding: 0.1em;">';
		echo '<h3 class="category_header">';
		echo $article['title'];
		echo '</h3>';
		echo sprintf('<span class="views_text"> Views: %d | Comments: %d </span>', $article['views'], $article['comments']);
		echo sprintf('<span class="views_text"> | Written By: %s in %s | %s </span>', $article['member'], $article['category'], htmlTime($article['dt_published']));
		echo '<section>';
		echo '<article class="post_wrapper forumposts">';
		echo $article['body'];
		echo '</article>';
		echo '</section>';
		echo '</div>';
	}
	
	echo '</div>';

	if (!empty($context['page_index'])) {
		template_pagesection();
	}

}
