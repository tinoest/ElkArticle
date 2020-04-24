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

	$download = $context['downloads'];
    $portalDownloads  = new YAPortalTemplate("portalDownloadSingle.tpl");
    $portalDownloads->set('path',           YAPortalSEO::generateUrlString(array('action' => 'download', 'sa' => 'category', 'id' => $download['category_id']), true, true));
    $portalDownloads->set('views',          $download['views']);
    $portalDownloads->set('comments',       ( $context['comments-enabled'] == 1 ) ? ' | '.$txt['yaportal-comments'] . $download['comments'] : '' );
    $portalDownloads->set('title',          $download['title']);
    $portalDownloads->set('category',       $download['category']);
    $portalDownloads->set('author',         $download['member']);
    $portalDownloads->set('body',           $download['body']);
    $portalDownloads->set('published',      htmlTime($download['dt_published']));
    $portalDownloads->set('download_link',  empty($download['download_link_src']) ? '' : $download['download_link_src']);
    $portalDownloads->set('download_name',  $txt['yaportal-download']);
    $portalDownloads->output();

	echo '</div>';

	if (!empty($context['page_index'])) {
		template_pagesection();
	}
}
