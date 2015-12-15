<?php
/**
 * File for class FT_List.
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
 * Database field, List (<select>) with contents in a table.
 *
 * @package Vortex
 * @subpackage DB
 */
class FT_List extends FT_Base
{
	/**
	 * Default value of the field.
	 *
	 * @var string
	 */
	var $default = '-1';

	/**
	 * Table to get the list from.
	 *
	 * @var string
	 */
	var $rel_table = '';

	/**
	 * Field to use as key in the table.
	 *
	 * @var string
	 */
	var $rel_key = '';

	/**
	 * Field to use as label in the table.
	 *
	 * @var string
	 */
	var $rel_label = '';

	/**
	 * Field to order by in the table.
	 *
	 * Default: {@link $rel_label}
	 *
	 * @var string
	 */
	var $rel_order = '';

	/**
	 * Constructor: Load all parameters into member variables.
	 *
	 * @param DB_Base $db Database where the field is.
	 * @param array $opts Parameters for the object, as 'var' => 'value'.
	 */
	function FT_List(&$db, $opts = array())
	{
		parent::FT_Base($db, $opts);
		is_null($this->rel_order) and $this->rel_order = $this->rel_label;
	}

	/**
	 * Output the field as a HTML Form.
	 *
	 * @param string $value Value to load the control with.
	 */
	function ShowForm($value, $origin = FT_OR_DB)
	{
		global $vortex_msgs;

		$sel = array(
			'fields' => array($this->rel_key, $this->rel_label),
			'from' => array($this->rel_table)
		);
		if (!empty($this->rel_order)) $sel['order'] = $this->rel_order;
		if (!($rs = $this->db->Select($sel))) {
			return;
		}
		echo "<select name='{$this->name_form}'>\n";
		echo "\t<option value='-1'".((-1 == $value)?' selected':'').">{$vortex_msgs['list_default']}</option>\n";
		while ($row = $rs->Row()) {
			echo "\t<option value='{$row[$this->rel_key]}'".(($row[$this->rel_key] == $value)?' selected':'').">{$row[$this->rel_label]}</option>\n";
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
		if (!($rs = $this->db->Select(array('from' => array($this->rel_table), 'fields' => array($this->rel_label), 'where' => "{$this->rel_key} = $value")))) {
			return;
		}
		$row = $rs->Row();
		echo $row[$this->rel_label];
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