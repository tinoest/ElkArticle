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

function template_yaportal_gallery_index()
{
	global $context, $txt, $scripturl, $boardurl;

    echo '<div class="elk_gallery_gridLayout">';

	if(is_array($context['galleries'])) {
        foreach($context['galleries'] as $gallery) {
            $urlPath        = YAPortalSEO::generateUrlString(array('action' => 'gallery', 'sa' => 'category', 'id' => $gallery['category_id']), true, true);
            $portalGallery  = new YAPortalTemplate("portalGallery.tpl");
            $portalGallery->set('path',             $urlPath);
            $portalGallery->set('views',            '');
            $portalGallery->set('comments',         '');
            $portalGallery->set('title',            $gallery['category_name']);
            $portalGallery->set('category',         $gallery['category_name']);
            $portalGallery->set('author',           $gallery['member']);
            $portalGallery->set('published',        htmlTime($gallery['dt_published']));
            $minFileName = str_replace('.jpg', '-min.jpg', $gallery['image_name']);
            if(file_exists(BOARDDIR . '/yaportal/img/thumbs/' . $minFileName)) {
                $portalGallery->set('image_path',   $boardurl . '/yaportal/img/thumbs/' . $minFileName);
            }
            else if(file_exists(BOARDDIR . '/yaportal/img/' . $gallery['image_name'])) {
                $portalGallery->set('image_path',   $boardurl . '/yaportal/img/' . $gallery['image_name']);
            }
            echo $portalGallery->output();
        }
	}
	echo '</div>';

	if (!empty($context['page_index'])) {
		template_pagesection();
	}
}

function template_yaportal_gallery()
{
	global $context, $txt, $scripturl, $boardurl;

    echo '<div class="elk_gallery_gridLayout">';

	foreach($context['galleries'] as $gallery) {
        $urlPath        = YAPortalSEO::generateUrlString(array('action' => 'gallery', 'sa' => 'image', 'id' => $gallery['id']), true, true);
        $portalGallery  = new YAPortalTemplate("portalGallery.tpl");
        $portalGallery->set('path',             $urlPath);
        $portalGallery->set('title',            $gallery['title']);
        $portalGallery->set('views',            'Views: '.$gallery['views']);
        $portalGallery->set('comments',         ( $context['comments-enabled'] == 1 ) ? ' | '.$txt['yaportal-comments'] . $gallery['comments'] .' | ' : ' | ');
        $portalGallery->set('category',         $gallery['category']);
        $portalGallery->set('author',           $gallery['member']);
        $portalGallery->set('published',        htmlTime($gallery['dt_published']));
        $minFileName = str_replace('.jpg', '-min.jpg', $gallery['image_name']);
        if(file_exists(BOARDDIR . '/yaportal/img/thumbs/' . $minFileName)) {
            $portalGallery->set('image_path',   $boardurl . '/yaportal/img/thumbs/' . $minFileName);
        }
        else if(file_exists(BOARDDIR . '/yaportal/img/' . $gallery['image_name'])) {
            $portalGallery->set('image_path',   $boardurl . '/yaportal/img/' . $gallery['image_name']);
        }
        echo $portalGallery->output();
	}
	echo '</div>';

	if (!empty($context['page_index'])) {
		template_pagesection();
	}
}

function template_yaportal_image()
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
        $exifData   = array();
		$gallery    = $context['gallery'];

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
                    $exifData = @exif_read_data(BOARDDIR . '/yaportal/img/' . $gallery['image_name']);
                    echo '<img src="' . $boardurl . '/yaportal/img/' . $gallery['image_name'] . '" height="90%" width="90%">';
                }

                echo '      </div>';

                if(!empty($gallery['body'])) {
                    echo '<div style="margin : 0.5em"><b>Description:</b><hr>'.$gallery['body'].'</div>';
                }

                if(!empty($exifData)) {
                    echo '<div style="margin : 1em"><b>EXIF Data: </b><br><hr>';
                    exif_data($exifData);
                    echo '</div>';
                }

                echo '  </article>
                    </section>
			    </div>
		</div>';
	}
}

function exif_data($exifData) 
{
    foreach($exifData as $k => $v) {
        if(!is_array($v)) {
            if(in_array($k, array('MimeType', 'Height', 'Width', 'ApertureFNumber', 'Make', 'Model'), true)) {
                echo '<b>'.$k.':</b> '. $v.'<br>';
            }
        }
        else {
            exif_data($v);
        }
    }
}

function template_yaportal_raw_image()
{
    global $context;

    header('Content-type: ' . $context['image_mime_type']);
    echo $context['image_content'];

}
