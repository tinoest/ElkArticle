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

function template_elkarticle_index()
{
	global $context, $txt, $scripturl;
	
	echo '<div id="eb_view_articles">';

	foreach($context['articles'] as $article) {
		echo '
		<div class="ea_article">
			<h3 class="category_header"><a href="'.$scripturl.'/index.php?sa=article&article='.$article['id'].'">'.$article['title'].'</a></h3>';
			echo sprintf('<span class="views_text"> Views: %d | Comments: %d </span>', $article['views'], $article['comments']);
			echo sprintf('<span class="views_text"> | Written By: %s in %s | %s </span>', $article['member'], $article['category'], htmlTime($article['dt_published']));
			echo '<section><article class="post_wrapper forumposts"><div style="margin : 0.5em">'.$article['body'].'</div></article></section>
		</div>';
	}
	
	echo '</div>';

	if (!empty($context['page_index'])) {
		template_pagesection();
	}
}

function template_elkarticle()
{
	global $context, $txt;

	if(array_key_exists('article_error', $context) && !empty($context['article_error'])) {
		echo '
		<div id="eb_view_articles">
			<div class="ea_article">
				<h3 class="category_header">'.$context['article_error'].'</h3>
			</div>
		</div>';
	}
	else {
		$article = $context['article'];
		
		echo '
		<div id="eb_view_articles">
			<div class="ea_article">
				<h3 class="category_header">'.$article['title'].'</h3>';
				echo sprintf('<span class="views_text"> Views: %d | Comments: %d </span>', $article['views'], $article['comments']);
				echo sprintf('<span class="views_text"> | Written By: %s in %s | %s </span>', $article['member'], $article['category'], htmlTime($article['dt_published']));
				echo '<section><article class="post_wrapper forumposts"><div style="margin : 0.5em">'.$article['body'].'</div></article></section>
			</div>
		</div>';
	}
}
