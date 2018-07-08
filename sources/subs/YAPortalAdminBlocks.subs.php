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

function delete_block($id)
{

	$db = database();

	$db->query('', '
		DELETE FROM {db_prefix}blocks
		WHERE id = {int:id}',
		array (
			'id'		=> $id,
		)
	);
}
