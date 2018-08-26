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

function template_yaportal_edit()
{
	global $settings, $context, $scripturl, $txt;

	echo '<link rel="stylesheet" type="text/css" href="'.$settings['theme_url'].'/css/pell.css">
		<h2 class="category_header">Post Image</h2>
		<div class="forumposts">
			<form id="gallery_form_edit" action="'.$scripturl.'?action=admin;area=yaportalgallery;sa=editgallery;" value="Submit" method="post" accept-charset="UTF-8" enctype="multipart/form-data">';

			if(isset($context['gallery_id'])) {
				echo '<input type="hidden" name="id" value="'.$context['gallery_id'].'" />';
			}

			echo '<dl id="post_header">
				<dt class="clear"><label for="post_subject" id="caption_subject">Subject:</label></dt>';

				if(!empty($context['gallery_subject'])) {
					echo '<dd><input type="text" name="gallery_subject" value="'.$context['gallery_subject'].'" tabindex="1" size="80" maxlength="80" class="input_text" placeholder="Subject" required="required" /><br /></dd>';
				}
				else {
					echo '<dd><input type="text" name="gallery_subject" value="" tabindex="1" size="80" maxlength="80" class="input_text" placeholder="Subject" required="required" /></dd>';
				}
				echo '<dt class="clear"><label for="gallery_category">Gallery Category:</label></dt>';

				echo '<dd><select name="gallery_category">';
				if(!empty($context['gallery_categories']) && is_array($context['gallery_categories'])) {
					foreach($context['gallery_categories'] as $k => $v) {
						if($k == $context['gallery_category']) {
							echo '<option value="'.$k.'" selected>'.$v.'</option>';
						}
						else {
							echo '<option value="'.$k.'">'.$v.'</option>';
						}
					}
				}
				echo '</select></dd>
				<dt class="clear"><label for="gallery_status">Status:</label></dt>
				<dd><select name="gallery_status">';
				foreach( array( 0 => $txt['yaportal-disabled'] , 1 => $txt['yaportal-enabled'], 2 => $txt['yaportal-approval'] ) as $k => $v) {
					if($k == $context['gallery_status']) {
						echo '<option value="'.$k.'" selected>'.$v.'</option>';
					}
					else {
						echo '<option value="'.$k.'">'.$v.'</option>';
					}
				}
				echo '</select></dd>
				</dl>
				<input type="hidden" id="gallery_body" name="gallery_body" />
				<div id="editor_toolbar_container">
					<div id="eb_editor" class="eb_editor"></div>
				</div>
				<div id="post_confirm_buttons" class="submitbutton">
                    <div style="float: left;">
                        <input type="file" id="gallery_image" name="gallery_image" />
                    </div>
                    <div style="float: right;">
					    <input type="submit" formaction="?action=admin;area=yaportalgallery;sa=resizeimage;image='. $context['gallery_image_name'] . '" value="Resize Image">
					    <input type="submit" value="Submit">
                    </div>
				</div>
            <input type="hidden" name="'.$context['session_var'].'" value="'.$context['session_id'].'" />
			</form>
		</div>
		<script src="'.$settings['theme_url'].'/scripts/pell.js"></script>
		<script>
		var editor = window.pell.init({
			element: document.getElementById(\'eb_editor\'),
			defaultParagraphSeparator: \'p\',
			styleWithCSS: false,
			onChange: function (html) {
				document.getElementById(\'gallery_body\').value = html
			}
		})
		';
		if(!empty($context['gallery_body'])) {
			echo 'editor.content.innerHTML = '.JavaScriptEscape($context['gallery_body']);
		}
		echo '</script>';

        if(!empty($context['gallery_image_src'])) {
            echo '<h2> Current Image: </h2><img src="'. $context['gallery_image_src'] .'" alt="" height="50%" width="50%">';
        }
}

function template_yaportal_list()
{
	global $context;

	template_show_list('gallery_list');

}

function template_elkcategory_list()
{
	global $context;

	template_show_list('category_list');

}

function template_elkcategory_add()
{
	global $context, $scripturl, $txt;

	echo '
	<h2 class="category_header">Add Category</h2>
	<div class="forumposts">
		<form id="gallery_form_edit" action="'.$scripturl.'?action=admin;area=yaportalgallery;sa=addcategory;" value="Submit" method="post" accept-charset="UTF-8">
			<dl id="post_header">
				<dt class="clear"><label for="category_name">'.$txt['yaportal-category-name'].'</label></dt>
			<input type="text" name="category_name" value=""> </input>
			</dl>
			<dl id="post_header">
				<dt class="clear"><label for="category_desc">'.$txt['yaportal-category-desc'].'</label></dt>
				<input type="text" name="category_desc" value=""> </input>
			</dl>
			<dl id="post_header">
				<dt class="clear"><label for="category_desc">'.$txt['yaportal-category-status'].'</label></dt>
				<input type="checkbox" name="category_enabled" '.(!empty($context['category_enabled']) ? 'checked' : '').'> </input>
			</dl>
			<input type="hidden" name="'.$context['session_var'].'" value="'.$context['session_id'].'" />
			<div id="post_confirm_buttons" class="submitbutton">
					<input type="submit" value="Submit">
			</div>
		</form>
	</div>';
}

function template_elkcategory_edit()
{
	global $context, $scripturl, $txt;

	echo '
	<h2 class="category_header">Add Category</h2>
	<div class="forumposts">
		<form id="gallery_form_edit" action="'.$scripturl.'?action=admin;area=yaportalgallery;sa=editcategory;" value="Submit" method="post" accept-charset="UTF-8">
			<input type="hidden" name="category_id" value="'.$context['category_id'].'"> </input>
			<dl id="post_header">
				<dt class="clear"><label for="category_name">'.$txt['yaportal-category-name'].'</label></dt>
			<input type="text" name="category_name" value="'.$context['category_name'].'"> </input>
			</dl>
			<dl id="post_header">
				<dt class="clear"><label for="category_desc">'.$txt['yaportal-category-desc'].'</label></dt>
				<input type="text" name="category_desc" value="'.$context['category_desc'].'"> </input>
			</dl>
			<dl id="post_header">
				<dt class="clear"><label for="category_desc">'.$txt['yaportal-category-status'].'</label></dt>
				<input type="checkbox" name="category_enabled" '.(!empty($context['category_enabled']) ? 'checked' : '').'> </input>
			</dl>
			<div id="post_confirm_buttons" class="submitbutton">
					<input type="submit" value="Submit">
			</div>
			<input type="hidden" name="'.$context['session_var'].'" value="'.$context['session_id'].'" />
		</form>
	</div>';
}
