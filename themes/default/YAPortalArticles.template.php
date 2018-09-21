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

function template_yaportal_article()
{
	global $context, $txt;

	if(array_key_exists('article_error', $context) && !empty($context['article_error'])) {
		echo '
		<div class="yaportal_article">
			<h3 class="category_header">'.$context['article_error'].'</h3>
		</div>';
	}
	else {
		$article = $context['article'];

		echo '
		<div class="yaportal_article">
			<h3 class="category_header">'.$article['title'].'</h3>';
			echo sprintf(
				'<span class="views_text"> Views: %d%s</span>', $article['views'],
				( $context['comments-enabled'] == 1 ) ? ' | '.$txt['yaportal-comments'] . $article['comments'] : ''
			);
			echo sprintf('<span class="views_text"> | Written By: %s in %s | %s </span>', $article['member'], $article['category'], htmlTime($article['dt_published']));
			echo '<section><article class="post_wrapper forumposts"><div style="margin : 0.5em">'.$article['body'].'</div></article></section>
		</div>';
	}
}
