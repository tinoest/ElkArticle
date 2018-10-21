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

    var_dump($context['downloads']);
	if(is_array($context['downloads'])) {
        foreach($context['downloads'] as $download) {
            echo '<div class="grid-item">';
            echo '<h3 class="category_header"><a href="'.$scripturl.'?download/'.$download['category_id'].'/">'.$download['category_name'].'</a></h3>';
            echo sprintf('<span class="views_text">Written By: %s in %s | %s </span>', $download['member'], $download['category_name'], htmlTime($download['dt_published']));
            echo '<hr>';
            if(!empty($download['download_download_link'])) {
                echo '<a href="'. $download['download_download_link'] .'" download>'.$txt['yaportal-download'].'</a>';
            }            
            echo '</div>';
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
