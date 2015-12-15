<?php
/**
 * File for class FT_Text.
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
 * Database field, Text type.
 *
 * @package Vortex
 * @subpackage DB
 */
class FT_Text extends FT_Base
{
	/**
	 * Maximum size of the field. Use -1 for unlimited.
	 *
	 * @var int
	 */
	var $size = -1;

	/**
	 * Number of rows in the text field.
	 *
	 * @var int
	 */
	var $rows = 1;

	/**
	 * Number of columns in the field. Use -1 to ignore.
	 *
	 * @var int
	 */
	var $cols = -1;

	/**
	 * Output the field as a HTML Form.
	 *
	 * @param string $value Value to load the control with.
	 */
	function ShowForm($value, $origin = FT_OR_DB)
	{
		if ($this->rows > 1) {
			echo "<textarea name='{$this->name_form}' rows='{$this->rows}'".(($this->cols > 0)?" cols='{$this->cols}'":'').">$value</textarea>";
		} else {
			echo "<input type='text' name='{$this->name_form}' value='$value'".(($this->size > 0)?" maxlength='{$this->size}'":'').' />';
		}
	}

	/**
	 * Output the field as plain text.
	 *
	 * @param string $value Value to load the control with.
	 */
	function ShowPlain($value, $origin = FT_OR_DB)
	{
		echo nl2br($value);
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
	 * Format the field for a WHERE clause.
	 *
	 * @param string $field The data from the field to be formated.
	 * @return string Returns the formated field.
	 */
	function Where(&$field)
	{
		return "{$this->name_db} LIKE '%".$this->db->AddSlashes($field)."%'";
	}
}

?>