<?php
/**
 * File for class SEC_Base.
 *
 * @package Vortex
 * @subpackage Page
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/**
 * Base class for page sections.
 *
 * @package Vortex
 * @subpackage Page
 */
class SEC_Base
{
	/**
	 * Child sections list.
	 * @var array
	 */
	var $sections = array();

	/**
	 * Constructor: Load all parameters into member variables.
	 *
	 * @param array $opts Parameters for the object, as 'var' => 'value'.
	 */
	function SEC_Base($opts = array())
	{
		$keys = array_keys($opts);
		foreach ($keys as $key) {
			$this->$key =& $opts[$key];
		}
	}

	/**
	 * Add a new child section.
	 *
	 * @param string $section Section name.
	 * @param SEC_Base $class Section class.
	 * @param array $opts Section class options.
	 * @param string $position Position on where to insert the section (insert before section $position).
	 */
	function AddSection($section, $class, $opts = array(), $position = NULL)
	{
		include_once($class.'.class.php');
		if (is_null($position)) {
			$this->sections[$section] = &new $class($opts);
		} else {
			$tmp = array();
			$found = false;
			foreach ($this->sections as $k => $i) {
				if (!strcmp($k, $position)) {
					$found = true;
					$tmp[$section] = &new $class($opts);
				}
				$tmp[$k] = &$this->sections[$k];
			}
			if (!$found) {
				$tmp[$section] = &new $class($opts);
			}
			$this->sections = &$tmp;
		}
	}

	/**
	 * Remove a child section from the section.
	 *
	 * @param string $section Section name.
	 */
	function DelSection($section)
	{
		unset($this->sections[$section]);
	}

	/**
	 * Outputs the section to the client.
	 *
	 * Show all childs in order.
	 */
	function Show()
	{
		foreach ($this->sections as $sec) {
			$sec->Show();
		}
	}
}

?>