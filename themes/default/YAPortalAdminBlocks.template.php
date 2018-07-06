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

function template_elkblock_list()
{
	global $context;

	template_show_list('block_list');

}

function template_yaportalblock_add()
{
	global $context, $scripturl, $txt;

	echo '
	<h2 class="block_header">Add Category</h2>
	<div class="forumposts">
		<form id="article_form_edit" action="'.$scripturl.'?action=admin;area=yaportalblocks;sa=addblock;" value="Submit" method="post" accept-charset="UTF-8">
			<dl id="post_header">
				<dt class="clear"><label for="block_name">'.$txt['yaportal-block-name'].'</label></dt>
			<input type="text" name="block_name" value=""> </input>
			</dl>
			<dl id="post_header">
				<dt class="clear"><label for="block_desc">'.$txt['yaportal-block-desc'].'</label></dt>
				<input type="text" name="block_desc" value=""> </input>
			</dl>
			<dl id="post_header">
				<dt class="clear"><label for="block_desc">'.$txt['yaportal-block-status'].'</label></dt>
				<input type="checkbox" name="block_enabled" '.(!empty($context['block_enabled']) ? 'checked' : '').'> </input>
			</dl>
			<input type="hidden" name="'.$context['session_var'].'" value="'.$context['session_id'].'" />
			<div id="post_confirm_buttons" class="submitbutton">
					<input type="submit" value="Submit">
			</div>
		</form>
	</div>';
}

function template_yaportalblock_edit()
{
	global $context, $scripturl, $txt;

	echo '
	<h2 class="block_header">Add Category</h2>
	<div class="forumposts">
		<form id="article_form_edit" action="'.$scripturl.'?action=admin;area=yaportalblocks;sa=editblock;" value="Submit" method="post" accept-charset="UTF-8">
			<input type="hidden" name="block_id" value="'.$context['block_id'].'"> </input>
			<dl id="post_header">
				<dt class="clear"><label for="block_name">'.$txt['yaportal-block-name'].'</label></dt>
			<input type="text" name="block_name" value="'.$context['block_name'].'"> </input>
			</dl>
			<dl id="post_header">
				<dt class="clear"><label for="block_desc">'.$txt['yaportal-block-desc'].'</label></dt>
				<input type="text" name="block_desc" value="'.$context['block_desc'].'"> </input>
			</dl>
			<dl id="post_header">
				<dt class="clear"><label for="block_desc">'.$txt['yaportal-block-status'].'</label></dt>
				<input type="checkbox" name="block_enabled" '.(!empty($context['block_enabled']) ? 'checked' : '').'> </input>
			</dl>
			<div id="post_confirm_buttons" class="submitbutton">
					<input type="submit" value="Submit">
			</div>
			<input type="hidden" name="'.$context['session_var'].'" value="'.$context['session_id'].'" />
		</form>
	</div>';
}
