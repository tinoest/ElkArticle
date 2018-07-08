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

class YAPortalAdminBlocks_Controller extends Action_Controller
{
	public function action_index()
	{
		require_once(SUBSDIR . '/Action.class.php');
		// Where do you want to go today?
		$subActions = array(
			'index' 		    => array($this, 'action_default'),
			'listblock' 		=> array($this, 'action_list_block'),
			'addblock' 		    => array($this, 'action_add_block'),
			'editblock' 		=> array($this, 'action_edit_block'),
			'deleteblock' 		=> array($this, 'action_delete_block'),
		);
		// We like action, so lets get ready for some
		$action = new Action('');
		// Get the subAction, or just go to action_index
		$subAction = $action->initialize($subActions, 'index');
		// Finally go to where we want to go

		$action->dispatch($subAction);
	}

	public function action_default()
	{
		$this->action_list_block();
	}

	public function action_list_block()
	{
		global $context, $scripturl, $txt;

		$list = array (
			'id' => 'block_list',
			'title' => $txt['yaportal-blocks'],
			'items_per_page' => 25,
			'no_items_label' => $txt['yaportal-notfound'],
			'base_href' => $scripturl . '?action=admin;area=yaportalblocks;sa=listblock;',
			'default_sort_col' => 'name',
			'get_items' => array (
				'function' => array($this, 'list_blocks'),
			),
			'get_count' => array (
				'function' => array($this, 'list_total_blocks'),
			),
			'columns' => array (
				'name' => array (
					'header' => array (
						'value' => 'Name',
					),
					'data' => array (
						'db' => 'name',
					),
					'sort' => array (
						'default' => 'name ASC',
						'reverse' => 'name DESC',
					),
				),
				'description' => array(
					'header' => array(
						'value' => 'Description',
					),
					'data' => array(
						'db' => 'description',
					),
					'sort' => array(
						'default' => 'description ASC',
						'reverse' => 'description DESC',
					),
				),
				'blocks' => array(
					'header' => array(
						'value' => 'Blocks',
					),
					'data' => array(
						'db' => 'blocks',
					),
					'sort' => array(
						'default' => 'blocks ASC',
						'reverse' => 'blocks DESC',
					),
				),
				'status' => array(
					'header' => array(
						'value' => 'Status',
						'class' => 'centertext',
					),
					'data' => array(
						'db' => 'status',
						'class' => 'centertext',
					),
					'sort' => array(
						'default' => 'status ASC',
						'reverse' => 'status DESC',
					),
				),
				'action' => array(
					'header' => array(
						'value' => 'Actions',
						'class' => 'centertext',
					),
					'data' => array(
						'sprintf' => array (
							'format' => '
								<a href="?action=admin;area=yaportalblocks;sa=editblock;block_id=%1$s;' . $context['session_var'] . '=' . $context['session_id'] . '" accesskey="p">Modify</a>&nbsp;
								<a href="?action=admin;area=yaportalblocks;sa=deleteblock;block_id=%1$s;' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="return confirm(' . JavaScriptEscape('Are you sure you want to delete?') . ') && submitThisOnce(this);" accesskey="d">Delete</a>',
							'params' => array(
								'id' => true,
							),
						),
						'class' => 'centertext nowrap',
					),
				),
			),
			'form' => array(
				'href' => $scripturl . '?action=admin;area=yaportalblocks;sa=addblock',
				'include_sort' => true,
				'include_start' => true,
				'hidden_fields' => array(
					$context['session_var'] => $context['session_id'],
				),
			),
			'additional_rows' => array(
				array(
					'position' => 'below_table_data',
					'value' => '<input type="submit" name="action_add_category" value="' . $txt['yaportal-addblock'] . '" class="right_submit" />',
				),
			),
		);

		$context['page_title']		= 'Block List';
		$context['sub_template'] 	= 'elkblock_list';
		$context['default_list'] 	= 'block_list';
		// Create the list.
		require_once(SUBSDIR . '/GenericList.class.php');
		createList($list);
		loadTemplate('YAPortalAdminBlocks');
	}

	public function action_add_block()
	{
		global $context;

		$this->action_list_block();
	}

	public function action_edit_block()
	{
		global $context;

		$this->action_list_block();
	}

	public function action_delete_block()
	{

		$this->action_list_block();
		return;

		require_once(SUBSDIR . '/YAPortalAdminBlocks.subs.php');
		if (!empty($_GET['block_id'])) {
			if (checkSession('get', '', false) !== '') {
				return;
			}

			$id	=  $_GET['block_id'];
			delete_block($id);
		}

		// Just Load the list again
		$this->action_list_block();
	}

	public function list_blocks($start, $items_per_page, $sort)
	{
		require_once(SUBSDIR . '/YAPortal.subs.php');
		return get_block_list($start, $items_per_page, $sort);
	}

	public function list_total_blocks()
	{
		require_once(SUBSDIR . '/YAPortal.subs.php');
		return get_total_blocks();
	}
}
