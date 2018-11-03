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

function template_yaportal_above()
{
    global $context, $txt, $scripturl;

	if(empty($context['yaportal_rightPanel']) && empty($context['yaportal_leftPanel'])) {
		$style = 'style="grid-column: span 3"';
	}
	else if(empty($context['yaportal_rightPanel']) || empty($context['yaportal_leftPanel'])) {
		$style = 'style="grid-column: span 2"';
	}
	else {
		$style = 'style="grid-column: span 1"';
	}

    $portalAbove    = new YAPortalTemplate("portalAbove.tpl");
	if(!empty($context['yaportal_topPanel'])) {
        $portalTop  = new YAPortalTemplate("portalTop.tpl");
        $portalTop->set('content_topPanel_header',  $context['yaportal_topPanel']['title']);
        $portalTop->set('content_topPanel',         $context['yaportal_topPanel']['content']);
        $portalAbove->set('yaportal_topPanel',      $portalTop->output());
    }
    else {
        $portalAbove->set('yaportal_topPanel',    '');
    }
	if(!empty($context['yaportal_rightPanel'])) {
        $portalRight    = new YAPortalTemplate("portalRight.tpl");
        $portalRight->set('content_rightPanel_header',  $context['yaportal_rightPanel']['title']);
        $portalRight->set('content_rightPanel',         $context['yaportal_rightPanel']['content']);
        $portalAbove->set('yaportal_rightPanel',        $portalRight->output());
    }
    else {
        $portalAbove->set('yaportal_rightPanel',    '');

    }
    $portalAbove->set('content_centerPanel_style',  $style);
    echo $portalAbove->output();

}


function template_yaportal_below()
{
    global $context, $txt, $scripturl;

	echo '</div>';

	if(!empty($context['yaportal_leftPanel'])) {
        $portalLeft    = new YAPortalTemplate("portalLeft.tpl");
    	$portalLeft->set('content_leftPanel_header',  $context['yaportal_leftPanel']['title']);
        $portalLeft->set('content_leftPanel',         $context['yaportal_leftPanel']['content']);
        echo $portalLeft->output();	
	}

	if(!empty($context['yaportal_bottomPanel'])) {
        $portalBelow    = new YAPortalTemplate("portalBottom.tpl");
    	$portalBelow->set('content_bottomPanel_header',  $context['yaportal_bottomPanel']['title']);
        $portalBelow->set('content_bottomPanel',         $context['yaportal_bottomPanel']['content']);
        echo $portalBelow->output();
	}


}

function template_yaportal_index()
{
	global $context, $txt, $scripturl;

	foreach($context['articles'] as $article) {
        $portalMain = new YAPortalTemplate("portalMain.tpl");
        $portalMain->set('path',        $scripturl.'?article/'.$article['id'].'/');
        $portalMain->set('views',       $article['views']);
        $portalMain->set('comments',    ( $context['comments-enabled'] == 1 ) ? ' | '.$txt['yaportal-comments'] . $article['comments'] : '');
        $portalMain->set('author',      $article['member']);
        $portalMain->set('category',    $article['category']);
        $portalMain->set('published',   htmlTime($article['dt_published']));
        $portalMain->set('title',       $article['title']);
        $portalMain->set('body',        $article['body']);
        echo $portalMain->output();
	}

	if (!empty($context['page_index'])) {
		template_pagesection();
	}

}
