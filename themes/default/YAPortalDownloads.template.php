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
            echo '<div class="grid-item">';
            echo '<h3 class="category_header"><a href="'.$scripturl.'?download/'.$download['category_id'].'/">'.$download['category_name'].'</a></h3>';
            echo sprintf('<span class="views_text">Written By: %s in %s | %s </span>', $download['member'], $download['category_name'], htmlTime($download['dt_published']));
            $minFileName = str_replace('.jpg', '-min.jpg', $download['image_name']);
            if(file_exists(BOARDDIR . '/yaportal/img/thumbs/' . $minFileName)) {
                echo '<div align="center"><img src="' . $boardurl . '/yaportal/img/thumbs/' . $minFileName . '" height="auto" width="90%"></div>';
            }
            else if(file_exists(BOARDDIR . '/yaportal/img/' . $download['image_name'])) {
                echo '<div align="center"><img src="' . $boardurl . '/yaportal/img/' . $download['image_name'] . '" height="auto" width="90%"></div>';
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
		echo '<h3 class="category_header"><a href="'.$scripturl.'?download/image/'.$download['id'].'/">'.$download['title'].'</a></h3>';
		echo sprintf(
			'<span class="views_text"> Views: %d%s', $download['views'],
			( $context['comments-enabled'] == 1 ) ? ' | '.$txt['yaportal-comments'] . $download['comments'] : ''
		);
		echo sprintf(' | Written By: %s in %s | %s </span>', $download['member'], $download['category'], htmlTime($download['dt_published']));
		$minFileName = str_replace('.jpg', '-min.jpg', $download['image_name']);
        if(file_exists(BOARDDIR . '/yaportal/img/thumbs/' . $minFileName)) {
            echo '<div align="center"><img src="' . $boardurl . '/yaportal/img/thumbs/' . $minFileName . '" height="auto" width="90%"></div>';
		}
		else if(file_exists(BOARDDIR . '/yaportal/img/' . $download['image_name'])) {
            echo '<div align="center"><img src="' . $boardurl . '/yaportal/img/' . $download['image_name'] . '" height="auto" width="90%"></div>';
        }
        echo '</div>';
	}
	echo '</div>';

	if (!empty($context['page_index'])) {
		template_pagesection();
	}
}

function template_yaportal_image()
{
	global $context, $txt, $boardurl;

	if(array_key_exists('download_error', $context) && !empty($context['download_error'])) {
		echo '
		<div id="eb_view_downloads">
			<div class="ea_download">
				<h3 class="category_header">'.$context['download_error'].'</h3>
			</div>
		</div>';
	}
	else {
        $exifData   = array();
		$download    = $context['download'];

		echo '
		<div id="eb_view_downloads">
			<div class="ea_download">
				<h3 class="category_header">'.$download['title'].'</h3>';
				echo sprintf(
					'<span class="views_text"> Views: %d%s</span>', $download['views'],
					( $context['comments-enabled'] == 1 ) ? ' | '.$txt['yaportal-comments'] . $download['comments'] : ''
				);
				echo sprintf('<span class="views_text"> | Written By: %s in %s | %s </span>', $download['member'], $download['category'], htmlTime($download['dt_published']));
				echo '<section>
                        <article class="post_wrapper forumposts">
                            <div align="center">';
                if(file_exists(BOARDDIR . '/yaportal/img/' . $download['image_name'])) {
                    $exifData = @exif_read_data(BOARDDIR . '/yaportal/img/' . $download['image_name']);
                    echo '<img src="' . $boardurl . '/yaportal/img/' . $download['image_name'] . '" height="90%" width="90%">';
                }

                echo '      </div>';

                if(!empty($download['body'])) {
                    echo '<div style="margin : 0.5em"><b>Description:</b><hr>'.$download['body'].'</div>';
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
