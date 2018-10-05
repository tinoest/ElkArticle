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

class YAPortalTemplate {
	protected $templatePath = BOARDDIR.'/yaportal/templates/';
	protected $file;
	protected $values = array();

	public function __construct($file) {
		$this->file = $file;
	}

	public function set($key, $value) {
		$this->values[$key] = $value;
	}

	public function output() {

		if (!file_exists($this->templatePath.$this->file)) {
			return false;
		}

		$output = file_get_contents($this->templatePath.$this->file);


		foreach ($this->values as $key => $value) {
			$tagToReplace	= "[@$key]";
			$output 	= str_replace($tagToReplace, $value, $output);
		}

		return $output;
	}
}

?>
