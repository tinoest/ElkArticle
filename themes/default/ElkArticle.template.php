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

	echo '<div class="elk_article_container">';
	
	if(!empty($context['elkarticle_topPanel'])) {
		echo '
		<div class="elk_article_topPanel">
			<h3 class="category_header">'.$context['elkarticle_topPanel']['title'].'</h3>
			'.$context['elkarticle_topPanel']['content'].'
		</div>';
	}

	if(!empty($context['elkarticle_rightPanel'])) {
    		echo '
		<div class="elk_article_rightPanel">
			<h3 class="category_header">'.$context['elkarticle_rightPanel']['title'].'</h3>
			'.$context['elkarticle_rightPanel']['content'].'
		</div>';
	}

	if(empty($context['elkarticle_rightPanel']) && empty($context['elkarticle_leftPanel'])) {	
		$style = 'style="grid-column: span 3"';
	}
	else {
		$style = '';
	}

	echo'
	<div class="elk_article_centerPanel" '.$style.'>';

	foreach($context['articles'] as $article) {
		echo '<h3 class="category_header"><a href="'.$scripturl.'?article/'.$article['id'].'/">'.$article['title'].'</a></h3>';
		echo sprintf(
			'<span class="views_text"> Views: %d%s</span>', $article['views'], 
			( $context['comments-enabled'] == 1 ) ? ' | '.$txt['elkarticle-comments'] . $article['comments'] : ''
		);
		echo sprintf('<span class="views_text"> | Written By: %s in %s | %s </span>', $article['member'], $article['category'], htmlTime($article['dt_published']));
		echo '<section><article class="post_wrapper forumposts"><div style="margin : 0.5em">'.$article['body'].'</div></article></section>';

		
	}

	echo '</div>';

	if (!empty($context['page_index'])) {
		template_pagesection();
	}


	if(!empty($context['elkarticle_leftPanel'])) {
    		echo '
		<div class="elk_article_leftPanel">
			<h3 class="category_header">'.$context['elkarticle_leftPanel']['title'].'</h3>
			'.$context['elkarticle_leftPanel']['content'].'
		</div>';
	}
	
	if(!empty($context['elkarticle_bottomPanel'])) {
    		echo '
		<div class="elk_article_bottomPanel">
			<h3 class="category_header">'.$context['elkarticle_bottomPanel']['title'].'</h3>
			'.$context['elkarticle_bottomPanel']['content'].'
		</div>';
	}

	echo '</div>';
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
				echo sprintf(
					'<span class="views_text"> Views: %d%s</span>', $article['views'], 
					( $context['comments-enabled'] == 1 ) ? ' | '.$txt['elkarticle-comments'] . $article['comments'] : ''
				);
				echo sprintf('<span class="views_text"> | Written By: %s in %s | %s </span>', $article['member'], $article['category'], htmlTime($article['dt_published']));
				echo '<section><article class="post_wrapper forumposts"><div style="margin : 0.5em">'.$article['body'].'</div></article></section>
			</div>
		</div>';
	}
}
