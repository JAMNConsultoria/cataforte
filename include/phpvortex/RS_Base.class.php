<?php
/**
 * File for class RS_Base.
 *
 * @package Vortex
 * @subpackage DB
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/**
 * Return an array with numeric indexes.
 * Used by {@link RS_Base::Row()} and {@link RS_Base::All()}.
 */
define('RS_ROW_NUM', 1);
/**
 * Return an array with field names as index.
 * Used by {@link RS_Base::Row()} and {@link RS_Base::All()}.
 */
define('RS_ROW_ASSOC', 2);
/**
 * Return an array with both numeric and field name indexes.
 * Used by {@link RS_Base::Row()} and {@link RS_Base::All()}.
 */
define('RS_ROW_BOTH', 3);

/**
 * Base class for RecordSets.
 *
 * @package Vortex
 * @subpackage DB
 */
class RS_Base
{
	/**
	 * Database.
	 * @var DB_Base
	 */
	var $db;

	/**
	 * Result from query.
	 * @var mixed
	 */
	var $result;

	/**
	 * Current row.
	 * @var int
	 */
	var $row;

	/**
	 * Constructor: Init the RecordSet from the result of a query.
	 *
	 * @param DB_Base $db Database.
	 * @param mixed $result Result from query.
	 */
	function RS_Base(&$db, $result)
	{
		$this->db =& $db;
		$this->result = $result;
		$this->row = 0;
	}

	/**
	 * Get the count of rows in the RecordSet.
	 *
	 * @abstract
	 * @return int Returns the number of rows in the RecordSet.
	 */
	function RowCount()
	{
	}

	/**
	 * Get a row from the RecordSet.
	 *
	 * Case $row is set, return that row, case else, return the next row.
	 *
	 * @abstract
	 * @param int $row Row to return, defaults to next.
	 * @param int $type Type of array to return (RS_ROW_NUM | RS_ROW_ASSOC | RS_ROW_BOTH).
	 * @return array Returns the row from the RecordSet, or FALSE if EOF.
	 */
	function Row($row = -1, $type = RS_ROW_ASSOC)
	{
	}

	/**
	 * Go to a row int the RecordSet.
	 *
	 * @abstract
	 * @param int $row Row to go to.
	 * @return bool Returns TRUE on success, FALSE if failed.
	 */
	function SetRow($row = 0)
	{
	}

	/**
	 * Get all rows from the RecordSet.
	 *
	 * @abstract
	 * @param int $type Type of array to return (RS_ROW_NUM | RS_ROW_ASSOC | RS_ROW_BOTH).
	 * @return array Returns all the rows from the RecordSet.
	 */
	function All($type = RS_ROW_ASSOC)
	{
	}

	/**
	 * Get the last auto-generated ID from the RecordSet.
	 *
	 * @abstract
	 * @return int Returns the last auto-generated ID from the RecordSet.
	 */
	function LastId()
	{
	}

	/**
	 * Get the last error message from the RecordSet.
	 *
	 * @abstract
	 * @return string Returns a string describing the last error that occurred in the RecordSet.
	 */
	function Error()
	{
		return FALSE;
	}

	/**
	 * Close the RecordSet and free the memory.
	 *
	 * @abstract
	 * @return bool Returns TRUE if the RecordSet was closed, FALSE if it failed.
	 */
	function Close()
	{
		return FALSE;
	}
}

?>