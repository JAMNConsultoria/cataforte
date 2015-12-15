<?php
/**
 * File for class RS_MySQL.
 *
 * @package Vortex
 * @subpackage DB
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/** Require the base class */
require_once('RS_Base.class.php');

/**
 * Class for MySQL RecordSets.
 *
 * @package Vortex
 * @subpackage DB
 */
class RS_MySQL extends RS_Base
{
	/**
	 * Get the count of rows in the RecordSet.
	 *
	 * @return int Returns the number of rows in the RecordSet.
	 */
	function RowCount()
	{
		return mysql_num_rows($this->result);
	}

	/**
	 * Get a row from the RecordSet.
	 *
	 * Case $row is set, return that row, case else, return the next row.
	 *
	 * @param int $row Row to return, defaults to next.
	 * @param int $type Type of array to return (RS_ROW_NUM | RS_ROW_ASSOC | RS_ROW_BOTH).
	 * @return array Returns the row from the RecordSet, or FALSE if EOF.
	 */
	function Row($row = -1, $type = RS_ROW_ASSOC)
	{
		if ($row != -1) {
			if (!mysql_data_seek($this->result, $row)) return FALSE;
			$this->row = $row;
		}
		$this->row++;
		switch ($type) {
		case RS_ROW_NUM:
			return mysql_fetch_row($this->result);
			break;
		case RS_ROW_ASSOC:
			return mysql_fetch_assoc($this->result);
			break;
		case RS_ROW_BOTH:
			return mysql_fetch_array($this->result);
			break;
		}
		return FALSE;
	}

	/**
	 * Go to a row int the RecordSet.
	 *
	 * @param int $row Row to go to.
	 * @return bool Returns TRUE on success, FALSE if failed.
	 */
	function SetRow($row = 0)
	{
		if (!mysql_num_rows($this->result)) return FALSE;
		if (!mysql_data_seek($this->result, $row)) return FALSE;
		$this->row = $row;
		return TRUE;
	}

	/**
	 * Get all rows from the RecordSet.
	 *
	 * @param int $type Type of array to return (RS_ROW_NUM | RS_ROW_ASSOC | RS_ROW_BOTH).
	 * @return array Returns all the rows from the RecordSet.
	 */
	function All($type = RS_ROW_ASSOC)
	{
		$rows = array();
		$this->SetRow();
		while ($row = $this->Row(-1, $type)) $rows[] = $row;
		return $rows;
	}

	/**
	 * Get the last auto-generated ID from the RecordSet.
	 *
	 * @return int Returns the last auto-generated ID from the RecordSet.
	 */
	function LastId()
	{
		return mysql_insert_id($this->db->link);
	}

	/**
	 * Get the last error message from the RecordSet.
	 *
	 * @return string Returns a string describing the last error that occurred in the RecordSet.
	 */
	function Error()
	{
		return mysql_error($this->db->link);
	}

	/**
	 * Close the RecordSet and free the memory.
	 *
	 * @return bool Returns TRUE if the RecordSet was closed, FALSE if it failed.
	 */
	function Close()
	{
		return mysql_free_result($this->result);
	}
}

?>