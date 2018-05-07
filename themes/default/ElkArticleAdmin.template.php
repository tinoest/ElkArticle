<?php

function template_elkarticle_edit()
{
	global $settings, $context, $scripturl;

	echo '<link rel="stylesheet" type="text/css" href="'.$settings['theme_url'].'/css/pell.css">
		<h2 class="category_header">Post Article</h2>
		<div class="forumposts">
			<form id="article_form_edit" action="'.$scripturl.'?action=admin;area=articleconfig;sa=editarticle;" value="Submit" method="post" accept-charset="UTF-8">';
	
			if(isset($context['article_id'])) {
				echo '<input type="hidden" name="article_id" value="'.$context['article_id'].'"> </input>';
			}
			
			echo '<dl id="post_header">
				<dt class="clear"><label for="post_subject" id="caption_subject">Subject:</label></dt>';

				if(!empty($context['article_subject'])) {
					echo '<dd><input type="text" name="article_subject" value="'.$context['article_subject'].'" tabindex="1" size="80" maxlength="80" class="input_text" placeholder="Subject" required="required"> </input><br /></dd>';
				}
				else {
					echo '<dd><input type="text" name="article_subject" value="" tabindex="1" size="80" maxlength="80" class="input_text" placeholder="Subject" required="required"> </input></dd>';
				}
				echo '<dt class="clear"><label for="article_category">Blog Category:</label></dt>';

				echo '<select name="article_category">';
				if(!empty($context['article_categories']) && is_array($context['article_categories'])) {
					foreach($context['article_categories'] as $k => $v) {
						if($k == $context['article_category']) {
							echo '<option value="'.$k.'" selected="selected">'.$v.'</option>';
						}
						else {
							echo '<option value="'.$k.'" selected="">'.$v.'</option>';
						}
					}
				}
				echo '</select>
				</dl>
				<input type="hidden" id="article_body" name="article_body" />
				<input type="hidden" name="'.$context['session_var'].'" value="'.$context['session_id'].'" />
				<div id="editor_toolbar_container">
					<div id="eb_editor" class="eb_editor"></div>
				</div>
				<div id="post_confirm_buttons" class="submitbutton">
					<input type="submit" value="Submit">
				</div>
			</form>
		</div>
		<script src="'.$settings['theme_url'].'/scripts/pell.js"></script>
		<script>
		var editor = window.pell.init({
			element: document.getElementById(\'eb_editor\'),
			defaultParagraphSeparator: \'p\',
			styleWithCSS: false,
			onChange: function (html) {
				document.getElementById(\'article_body\').value = html
			}
		})
		';
		if(!empty($context['article_body'])) {
			echo 'editor.content.innerHTML = '.JavaScriptEscape($context['article_body']);
		}
		echo '</script>';
}

function template_elkarticle_list()
{
	global $context;

	template_show_list('article_list');

}

function template_elkcategory_list()
{
	global $context;

	template_show_list('category_list');

}

function template_elkcategory_add()
{
	global $context, $scripturl;

	echo '
	<h2 class="category_header">Add Category</h2>
	<div class="forumposts">
		<form id="article_form_edit" action="'.$scripturl.'?action=admin;area=articleconfig;sa=addcategory;" value="Submit" method="post" accept-charset="UTF-8">
			<br /><label for="category_name">Category Name:</label>
			<input type="text" name="category_name" value=""> </input>
			<div id="post_confirm_buttons" class="submitbutton">
					<input type="submit" value="Submit">
			</div>
		</form>
	</div>';
}
