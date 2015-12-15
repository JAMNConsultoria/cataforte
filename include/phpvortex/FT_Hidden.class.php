<?php
/**
 * File for class FT_Hidden.
 *
 * @package Vortex
 * @subpackage DB
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/** Require the base class */
require_once('FT_Base.class.php');

/**
 * Database field, Hidden.
 *
 * @package Vortex
 * @subpackage DB
 */
class FT_Hidden extends FT_Base
{
	/**
	 * Output the field as a HTML Form or just for display.
	 *
	 * @param array $data Array containing the field values as 'field' => 'value'.
	 * @param bool $form Show field as a INPUT object?
	 */
	function Show(&$data, $form = TRUE, $origin = FT_OR_DB)
	{
		if (empty($data) || !isset($data[$this->name_db])) {
			$value = $this->default;
		} else {
			$value = $data[$this->name_db];
		}
		if ($form) {
			$this->ShowForm($value, $origin);
		} else {
			$this->ShowPlain($value, $origin);
		}
	}

	/**
	 * Output the field as a HTML Form.
	 *
	 * @param string $value Value to load the control with.
	 */
	function ShowForm($value, $origin = FT_OR_DB)
	{
		echo "<input type='hidden' name='{$this->name_form}' value='$value' />";
	}

	/**
	 * Test the field consistency.
	 *
	 * @param string $field The data from the field to be tested.
	 * @return bool Returns TRUE if the field is consistent, FALSE otherwise.
	 */
	function ConsistTest(&$field)
	{
		if ($this->required && empty($field)) return FALSE;
		return TRUE;
	}
}

?>