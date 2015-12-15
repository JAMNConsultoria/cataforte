<?php
/**
 * File for class FT_Password.
 *
 * @package Vortex
 * @subpackage DB
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2006, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/** Require the base class */
require_once('FT_Base.class.php');

/**
 * Database field, Password type.
 *
 * @package Vortex
 * @subpackage DB
 */
class FT_Password extends FT_Base
{
	/**
	 * Encription to use, use a SQL function.
	 *
	 * @var string
	 */
	var $enc = 'MD5';

	/**
	 * Output the field as a HTML Form.
	 *
	 * @param string $value Value to load the control with.
	 */
	function ShowForm($value, $origin = FT_OR_DB)
	{
		if ($origin == FT_OR_DB) {
			echo "<input type='password' name='{$this->name_form}' value='__VORTEX_PASS__$value' />";
		} else {
			echo "<input type='password' name='{$this->name_form}' value='$value' />";
		}
	}

	/**
	 * Output the field consistency testing in JavaScript.
	 */
	function JSConsist()
	{
		if ($this->required) {
			echo <<<END
	if (frm.{$this->name_form}.value == "") errors += " * {$this->label}\\n";

END;
		}
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

	/**
	 * Format the field for database insertion.
	 *
	 * @param string $field The data from the field to be formated.
	 * @return string Returns the formated field.
	 */
	function ConsistFormat(&$field)
	{
		if (strstr($field, '__VORTEX_PASS__') !== FALSE) {
			$tmp = substr($field, 15);
			return "'".$this->db->AddSlashes($tmp)."'";
		} else {
			return "{$this->enc}('".$this->db->AddSlashes($field)."')";
		}
	}
}

?>