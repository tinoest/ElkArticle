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

function template_yaportal_download_index()
{
	global $context, $txt, $scripturl, $boardurl;

    echo '<div class="elk_download_gridLayout">';
	if(is_array($context['downloads'])) {
        foreach($context['downloads'] as $download) {
            $portalDownloads  = new YAPortalTemplate("portalDownload.tpl");
            $portalDownloads->set('path',       YAPortalSEO::generateUrlString(array('action' => 'download', 'sa' => 'category', 'id' => $download['category_id']), true, true));
            $portalDownloads->set('views',      '');
            $portalDownloads->set('comments',   '');
            $portalDownloads->set('title',      $download['category_name']);
            $portalDownloads->set('category',   $download['category_name']);
            $portalDownloads->set('author',     $download['member']);
            $portalDownloads->set('published',  htmlTime($download['dt_published']));
            $portalDownloads->set('download',   empty($download['download_link_src']) ? '' : '<a href="'. $download['download_link_src'] .'" download>'.$txt['yaportal-download'].'</a>');
            $portalDownloads->output();
        }
	}
	echo '</div>';

	if (!empty($context['page_index'])) {
		template_pagesection();
	}
}

function template_yaportal_download()
{
	global $context, $txt, $scripturl, $boardurl;

    echo '<div class="elk_download_gridLayout">';

	foreach($context['downloads'] as $download) {
        echo '<div class="grid-item">';
		echo '<h3 class="category_header">', $download['title'], '</h3>';
		echo sprintf(
			'<span class="views_text"> Views: %d%s', $download['views'],
			( $context['comments-enabled'] == 1 ) ? ' | '.$txt['yaportal-comments'] . $download['comments'] : ''
		);
		echo sprintf(' | Written By: %s in %s | %s </span>', $download['member'], $download['category'], htmlTime($download['dt_published']));
        echo '<hr>';
        if(!empty($download['download_link_src'])) {
            echo '<a href="'. $download['download_link_src'] .'" download>'.$txt['yaportal-download'].'</a>';
        }            
        echo '</div>';
	}
	echo '</div>';

	if (!empty($context['page_index'])) {
		template_pagesection();
	}
}
