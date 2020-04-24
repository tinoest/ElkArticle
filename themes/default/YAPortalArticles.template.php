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
        $portalError = new YAPortalTemplate("portalError.tpl");
        $portalError->set('error',  $context['article_error']);
        $portalError->output();
	}
	else {
		$article = $context['article'];
        $portalArticle = new YAPortalTemplate("portalArticle.tpl");
        $portalArticle->set('path',        YAPortalSEO::generateUrlString(array('action' => 'article', 'sa' => 'view', 'id' => $article['id']), true, true));
        $portalArticle->set('views',       $article['views']);
        $portalArticle->set('comments',    ( $context['comments-enabled'] == 1 ) ? ' | '.$txt['yaportal-comments'] . $article['comments'] : '');
        $portalArticle->set('author',      $article['member']);
        $portalArticle->set('category',    $article['category']);
        $portalArticle->set('published',   htmlTime($article['dt_published']));
        $portalArticle->set('title',       $article['title']);
        $portalArticle->set('body',        $article['body']);
        $portalArticle->output();
        template_yaportal_comments();
    }
}

function template_yaportal_comments()
{

    $portalComments = new YAPortalTemplate("portalComments.tpl");
    $portalComments->set('post_url',    '');
    $portalComments->set('label',       '');
    $portalComments->set('comment',     '');
    $portalComments->output();

}
