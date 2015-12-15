<?php
/**
 * File for class FT_DateTime.
 *
 * @package Vortex
 * @subpackage DB
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2005, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/** Require the base class */
require_once('FT_Base.class.php');

/**
 * Date only field.
 * Used in {@link FT_DateTime::$type}.
 */
define('FT_DATE', 1);

/**
 * Time only field.
 * Used in {@link FT_DateTime::$type}.
 */
define('FT_TIME', 2);

/**
 * Combined date/time field.
 * Used in {@link FT_DateTime::$type}.
 */
define('FT_DATETIME', 3);

if (!function_exists('array_combine')) {
	function array_combine ($keys, $values) {
		if ( (count($keys) === count($values)) && (count($keys) > 0 && count($values) > 0) ) {
			$keys = array_values($keys);
			$values = array_values($values);
			for (@$i = 0; $i < count($values); $i++)
				@$return[$keys[$i]] =& $values[$i];
			return $return;
		} else {
			return false;
		}
	}
}

/**
 * Database field, Date/Time type.
 *
 * @package Vortex
 * @subpackage DB
 */
class FT_DateTime extends FT_Base
{
	/**
	 * Type of the field (FT_DATE|FT_TIME|FT_DATETIME).
	 *
	 * @var int
	 */
	var $type = FT_DATETIME;

	/**
	 * Format of the field, as in date().
	 *
	 * Defaults to:
	 * $type == FT_DATE		-> $vortex_msgs['ft_date']
	 * $type == FT_TIME		-> $vortex_msgs['ft_time']
	 * $type == FT_DATETIME	-> $vortex_msgs['ft_datetime']
	 *
	 * @var string
	 */
	var $format = NULL;

	/**
	 * Constructor: Load all parameters into member variables.
	 *
	 * @param DB_Base $db Database where the field is.
	 * @param array $opts Parameters for the object, as 'var' => 'value'.
	 */
	function FT_DateTime(&$db, $opts = array())
	{
		global $vortex_msgs;
		
		parent::FT_Base($db, $opts);
		
		if (is_null($this->format)) {
			switch ($this->type) {
			case FT_DATE:
				$this->format = $vortex_msgs['ft_date'];
				break;
			case FT_TIME:
				$this->format = $vortex_msgs['ft_time'];
				break;
			case FT_DATETIME:
				$this->format = $vortex_msgs['ft_datetime'];
				break;
			}
		}
	}

	/**
	 * Output the field as a HTML Form.
	 *
	 * @param string $value Value to load the control with.
	 */
	function ShowForm($value, $origin = FT_OR_DB)
	{
		if ($origin == FT_OR_DB) {
			echo "<input type='text' name='{$this->name_form}' value='".($this->ConsistTest($value)?date($this->format, strtotime($value)):$value)."' />";
		} else {
			echo "<input type='text' name='{$this->name_form}' value='$value' />";
		}
	}

	/**
	 * Output the field as plain text.
	 *
	 * @param string $value Value to load the control with.
	 */
	function ShowPlain($value, $origin = FT_OR_DB)
	{
		if ($origin == FT_OR_DB) {
			echo (empty($value)?'':date($this->format, strtotime($value)));
		} else {
			echo (empty($value)?'':$value);
		}
	}

	/**
	 * Output the field consistency testing in JavaScript.
	 */
	function JSConsist()
	{
		if ($this->required) {
			echo <<< END
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
		$f = preg_split('/[\s,.\/\\-:]/', strtolower($this->format), -1, PREG_SPLIT_NO_EMPTY);
		$v = preg_split('/[\s,.\/\\-:]/', $field, -1, PREG_SPLIT_NO_EMPTY);
		if (count($f) != count($v)) return FALSE;
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
		$f = preg_split('/[\s,.\/\\-:]/', strtolower($this->format), -1, PREG_SPLIT_NO_EMPTY);
		$v = preg_split('/[\s,.\/\\-:]/', $field, -1, PREG_SPLIT_NO_EMPTY);
		if (count($f) != count($v)) return "''";
		$dt = array_combine ($f, $v);
		switch ($this->type) {
		case FT_DATE:
			return "'".date('Y-m-d', mktime(0, 0, 0, $dt['m'], $dt['d'], $dt['y']))."'";
			break;
		case FT_TIME:
			return "'".date('H:i:s', mktime($dt['h'], $dt['i'], $dt['s']))."'";
			break;
		case FT_DATETIME:
			return "'".date('Y-m-d H:i:s', mktime($dt['h'], $dt['i'], $dt['s'], $dt['m'], $dt['d'], $dt['y']))."'";
			break;
		}
		return "''";
	}
}

?>