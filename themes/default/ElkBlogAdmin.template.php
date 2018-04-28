<?php

function template_elkblog_admin()
{
	global $settings, $context, $scripturl;

	echo '<link rel="stylesheet" type="text/css" href="'.$settings['theme_url'].'/css/pell.css">';
	echo '<div class="editor_wrapper" style="height:50em;">';
	
	echo '<form id="blog_form_edit" action="'.$scripturl.'?action=admin;area=blogconfig;sa=edit;" value="Submit" method="post" accept-charset="UTF-8">';
	echo 'Subject: <input type="text" name="blog_subject" value="'.$context['blog_subject'].'"> </input><br />';
	echo 'Body: <input type="hidden" id="blog_body" name="blog_body"> </input>';
	echo '<input type="hidden" name="'.$context['session_var'].'" value="'.$context['session_id'].'" />';
	echo '<div id="eb_editor" class="eb_editor"></div>';
	echo '<input type="submit" value="Submit">';
	echo '</form>';
	echo '</div>';
	echo '</div>';


	echo '<script src="'.$settings['theme_url'].'/scripts/pell.js"></script>';
	echo '
	<script>
      	var editor = window.pell.init({
        	element: document.getElementById(\'eb_editor\'),
        	defaultParagraphSeparator: \'p\',
        	styleWithCSS: false,
        	onChange: function (html) {
			document.getElementById(\'blog_body\').value = html
        	}
      	})
	editor.content.innerHTML = \''.$context['blog_body'].'\'
    	</script>';
}
