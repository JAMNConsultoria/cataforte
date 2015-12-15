<?php
/**
 * File for class FT_Base.
 *
 * @package Vortex
 * @subpackage DB
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/**
 * Base class for all database fields.
 *
 * @package Vortex
 * @subpackage DB
 */
class FT_Base
{
	/**
	 * Database where the field is.
	 *
	 * @var DB_Base
	 */
	var $db;

	/**
	 * Field name.
	 *
	 * @var string
	 */
	var $name;

	/**
	 * Name of the field in the database.
	 * Default = {@link $name}
	 *
	 * @var string
	 */
	var $name_db;

	/**
	 * Name of the field in the forms.
	 * Default = {@link $name}
	 *
	 * @var string
	 */
	var $name_form;

	/**
	 * Label to the field.
	 * Default = {@link $name}
	 *
	 * @var string
	 */
	var $label;

	/**
	 * Is the field a Primary Key?
	 *
	 * @var bool
	 */
	var $pkey = FALSE;

	/**
	 * Is the field required(obligatory)?
	 *
	 * @var bool
	 */
	var $required = FALSE;

	/**
	 * Default value of the field.
	 *
	 * @var string
	 */
	var $default = '';

	/**
	 * Constructor: Load all parameters into member variables.
	 *
	 * @param DB_Base $db Database where the field is.
	 * @param array $opts Parameters for the object, as 'var' => 'value'.
	 */
	function FT_Base(&$db, $opts = array())
	{
		$this->db =& $db;
		foreach ($opts as $key => $value) {
			$this->$key = $value;
		}
		is_null($this->name_db) and $this->name_db = $this->name;
		is_null($this->name_form) and $this->name_form = $this->name;
		is_null($this->label) and $this->label = $this->name;
	}

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
			echo "<tr class='ft_{$this->name}'><th>".$this->label.'</th><td>';
			$this->ShowForm($value, $origin);
			echo "</td></tr>\n";
		} else {
			echo $this->label.': ';
			$this->ShowPlain($value, $origin);
		}
	}

	/**
	 * Output the field as a HTML Form.
	 *
	 * @abstract
	 * @param string $value Value to load the control with.
	 */
	function ShowForm($value, $origin = FT_OR_DB)
	{
	}

	/**
	 * Output the field as plain text.
	 *
	 * @param string $value Value to load the control with.
	 */
	function ShowPlain($value, $origin = FT_OR_DB)
	{
		echo $value;
	}

	/**
	 * Output the field consistency testing in JavaScript.
	 *
	 * @abstract
	 */
	function JSConsist()
	{
	}

	/**
	 * Extract the field from $vars, test the field consistency and return it ready for database insertion.
	 *
	 * @param array $vars Array containing the FORM data (usually $_POST).
	 * @return string Returns a string containing the parsed field data, or FALSE if the field is invalid.
	 */
	function Consist(&$vars)
	{
		if (!isset($vars[$this->name_form])) {
			if ($this->required) {
				return FALSE;
			} else {
				return "'".$this->default."'";
			}
		}
		$field =& $vars[$this->name_form];
		if (!$this->ConsistTest($field)) return FALSE;
		return $this->ConsistFormat($field);
	}

	/**
	 * Test the field consistency.
	 *
	 * @param string $field The data from the field to be tested.
	 * @return bool Returns TRUE if the field is consistent, FALSE otherwise.
	 */
	function ConsistTest(&$field)
	{
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
		return "'".$this->db->AddSlashes($field)."'";
	}

	/**
	 * Format the field for a WHERE clause.
	 *
	 * @param string $field The data from the field to be formated.
	 * @return string Returns the formated field.
	 */
	function Where(&$field)
	{
		return "{$this->name_db} = ".$this->ConsistFormat($field);
	}
}

?>