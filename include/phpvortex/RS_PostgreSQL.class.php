<?php
/**
 * File for class RS_PostgreSQL.
 *
 * @package Vortex
 * @subpackage DB
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2005, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/** Require the base class */
require_once('RS_Base.class.php');

/**
 * Class for PostgreSQL RecordSets.
 *
 * @package Vortex
 * @subpackage DB
 */
class RS_PostgreSQL extends RS_Base
{
	/**
	 * Get the count of rows in the RecordSet.
	 *
	 * @return int Returns the number of rows in the RecordSet.
	 */
	function RowCount()
	{
		return pg_num_rows($this->result);
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
			$this->row = $row + 1;
			switch ($type) {
			case RS_ROW_NUM:
				return pg_fetch_row($this->result, $this->row - 1);
				break;
			case RS_ROW_ASSOC:
				return pg_fetch_assoc($this->result, $this->row - 1);
				break;
			case RS_ROW_BOTH:
				return pg_fetch_array($this->result, $this->row - 1);
				break;
			}
			return FALSE;
		}
		$this->row++;
		switch ($type) {
		case RS_ROW_NUM:
			return pg_fetch_row($this->result);
			break;
		case RS_ROW_ASSOC:
			return pg_fetch_assoc($this->result);
			break;
		case RS_ROW_BOTH:
			return pg_fetch_array($this->result);
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
		$this->row = $row;
		if (pg_result_seek($this->result, $row) === FALSE) {
			return FALSE;
		} else {
			return TRUE;
		}
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
		return pg_last_oid($this->result);
	}

	/**
	 * Get the last error message from the RecordSet.
	 *
	 * @return string Returns a string describing the last error that occurred in the RecordSet.
	 */
	function Error()
	{
		return pg_result_error($this->result);
	}

	/**
	 * Close the RecordSet and free the memory.
	 *
	 * @return bool Returns TRUE if the RecordSet was closed, FALSE if it failed.
	 */
	function Close()
	{
		return pg_free_result($this->result);
	}
}

?>