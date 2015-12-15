<?php
/**
 * File for class FT_ListArray.
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
 * Database field, List (<select>) with contents in a array.
 *
 * @package Vortex
 * @subpackage DB
 */
class FT_ListArray extends FT_Base
{
	/**
	 * Default value of the field.
	 *
	 * @var string
	 */
	var $default = '-1';

	/**
	 * Array to get the list from.
	 *
	 * List itens as 'key' => 'label'
	 *
	 * @var array
	 */
	var $list_array = array();

	/**
	 * Output the field as a HTML Form.
	 *
	 * @param string $value Value to load the control with.
	 */
	function ShowForm($value, $origin = FT_OR_DB)
	{
		global $vortex_msgs;

		echo "<select name='{$this->name_form}'>\n";
		echo "\t<option value='-1'".((-1 == $value)?' selected':'').">{$vortex_msgs['list_default']}</option>\n";
		foreach ($this->list_array as $key => $label) {
			echo "\t<option value='$key'".(($key == $value)?' selected':'').">$label</option>\n";
		}
		echo "</select>\n";
	}

	/**
	 * Output the field as plain text.
	 *
	 * @param string $value Value to load the control with.
	 */
	function ShowPlain($value, $origin = FT_OR_DB)
	{
		echo $this->list_array[$value];
	}

	/**
	 * Output the field consistency testing in JavaScript.
	 */
	function JSConsist()
	{
		if ($this->required) {
			echo <<<END
	if (frm.{$this->name_form}.selectedIndex < 1) errors += " * {$this->label}\\n";

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
		if ($this->required && (empty($field) || ($field < 0))) return FALSE;
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
		return (string)((int)$field);
	}
}

?>