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

function template_yaportal_index()
{
	global $context, $txt, $scripturl;

	echo '<div class="elk_gallery_container">';

	if(!empty($context['yaportal_topPanel'])) {
		echo '
		<div class="elk_gallery_topPanel">
			<h3 class="category_header">'.$context['yaportal_topPanel']['title'].'</h3>
			'.$context['yaportal_topPanel']['content'].'
		</div>';
	}

	if(!empty($context['yaportal_rightPanel'])) {
    		echo '
		<div class="elk_gallery_rightPanel">
			<h3 class="category_header">'.$context['yaportal_rightPanel']['title'].'</h3>
			'.$context['yaportal_rightPanel']['content'].'
		</div>';
	}

	if(empty($context['yaportal_rightPanel']) && empty($context['yaportal_leftPanel'])) {
		$style = 'style="grid-column: span 3"';
	}
	else if(empty($context['yaportal_rightPanel']) || empty($context['yaportal_leftPanel'])) {
		$style = 'style="grid-column: span 2"';
	}
	else {
		$style = 'style="grid-column: span 1"';
	}

	echo'
	<div class="elk_gallery_centerPanel" '.$style.'>';

	foreach($context['galleries'] as $gallery) {
		echo '<h3 class="category_header"><a href="'.$scripturl.'?gallery/'.$gallery['id'].'/">'.$gallery['title'].'</a></h3>';
		echo sprintf(
			'<span class="views_text"> Views: %d%s</span>', $gallery['views'],
			( $context['comments-enabled'] == 1 ) ? ' | '.$txt['yaportal-comments'] . $gallery['comments'] : ''
		);
		echo sprintf('<span class="views_text"> | Written By: %s in %s | %s </span>', $gallery['member'], $gallery['category'], htmlTime($gallery['dt_published']));
		echo '<section><article class="post_wrapper forumposts"><div style="margin : 0.5em">'.$gallery['body'].'</div></article></section>';


	}

	if (!empty($context['page_index'])) {
		template_pagesection();
	}

	echo '</div>';

	if(!empty($context['yaportal_leftPanel'])) {
    		echo '
		<div class="elk_gallery_leftPanel">
			<h3 class="category_header">'.$context['yaportal_leftPanel']['title'].'</h3>
			'.$context['yaportal_leftPanel']['content'].'
		</div>';
	}

	if(!empty($context['yaportal_bottomPanel'])) {
    		echo '
		<div class="elk_gallery_bottomPanel">
			<h3 class="category_header">'.$context['yaportal_bottomPanel']['title'].'</h3>
			'.$context['yaportal_bottomPanel']['content'].'
		</div>';
	}


	echo '</div>';
}

function template_yaportal()
{
	global $context, $txt, $boardurl;

	if(array_key_exists('gallery_error', $context) && !empty($context['gallery_error'])) {
		echo '
		<div id="eb_view_galleries">
			<div class="ea_gallery">
				<h3 class="category_header">'.$context['gallery_error'].'</h3>
			</div>
		</div>';
	}
	else {
		$gallery = $context['gallery'];

		echo '
		<div id="eb_view_galleries">
			<div class="ea_gallery">
				<h3 class="category_header">'.$gallery['title'].'</h3>';
				echo sprintf(
					'<span class="views_text"> Views: %d%s</span>', $gallery['views'],
					( $context['comments-enabled'] == 1 ) ? ' | '.$txt['yaportal-comments'] . $gallery['comments'] : ''
				);
				echo sprintf('<span class="views_text"> | Written By: %s in %s | %s </span>', $gallery['member'], $gallery['category'], htmlTime($gallery['dt_published']));
				echo '<section>
                        <article class="post_wrapper forumposts">
                            <div align="center">';
                if(file_exists(BOARDDIR . '/yaportal/img/' . $gallery['image_name'])) {
                    echo '<img src="' . $boardurl . '/yaportal/img/' . $gallery['image_name'] . '" height="90%" width="90%">';
                }

                echo '      </div>
                            <div style="margin : 0.5em">'.$gallery['body'].'</div>
                        </article>
                    </section>
			    </div>
		</div>';
	}
}

function template_yaportal_image()
{
    global $context;

    header('Content-type: ' . $context['image_mime_type']);
    echo $context['image_content'];

}
