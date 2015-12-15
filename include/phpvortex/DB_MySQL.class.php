<?php
/**
 * File for class DB_MySQL.
 *
 * @package Vortex
 * @subpackage DB
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/** Require the base class */
require_once('DB_Base.class.php');

/** Require the RecordSet class header. */
require_once('RS_MySQL.class.php');

/**
 * Class for MySQL database connection.
 *
 * @package Vortex
 * @subpackage DB
 */
class DB_MySQL extends DB_Base
{
	/**
	 * Open a new connection to the database if not already connected.
	 *
	 * @param bool $persist Open a persistent connection?
	 * @return bool Returns TRUE if the connection was successfully established, FALSE if an error occurred.
	 */
	function Connect($persist=FALSE)
	{
		if (!is_null($this->link)) return FALSE;
		if ($persist) {
			$this->link = mysql_pconnect($this->server, $this->user, $this->pw);
		} else {
			$this->link = mysql_connect($this->server, $this->user, $this->pw, TRUE);
		}
		if ($this->link !== FALSE) {
			if (mysql_select_db($this->db, $this->link)) {
				return TRUE;
			}
			mysql_close($this->link);
		}
		$this->link = NULL;
		return FALSE;
	}

	/**
	 * Execute a query at the database.
	 *
	 * @param string $sql Query to run.
	 * @return RS_Base Returns a RecordSet object if there is a result to the query or TRUE if it was successfull, FALSE if a error occurred.
	 */
	function &Query($sql)
	{
		$rs = mysql_query($sql,$this->link);
		if ($rs === FALSE) {
			return FALSE;
		}
		return new RS_MySQL($this, $rs);
	}

	/**
	 * Close the connection to the database if still connected.
	 *
	 * @return bool Returns TRUE if the connection was closed, FALSE if it failed.
	 */
	function Close()
	{
		if (mysql_close($this->link)) {
			$this->link = NULL;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get the last error message from the connection.
	 *
	 * @return string Returns a string describing the last error that occurred in the connection.
	 */
	function Error()
	{
		return mysql_error($this->link);
	}

	/**
	 * Transactions: Begin a new transaction.
	 *
	 * @return bool Returns TRUE if the new transaction began, FALSE if it failed.
	 */
	function Begin()
	{
		return $this->Query('BEGIN');
	}

	/**
	 * Transactions: Commit a transaction.
	 *
	 * @return bool Returns TRUE if the transaction was commited, FALSE if it failed.
	 */
	function Commit()
	{
		return $this->Query('COMMIT');
	}

	/**
	 * Transactions: Rollback a transaction.
	 *
	 * @return bool Returns TRUE if the transaction was cancelled, FALSE if it failed.
	 */
	function Rollback()
	{
		return $this->Query('ROLLBACK');
	}

	/**
	 * Process a string for safe use in a database insertion.
	 *
	 * @param string $data The string to be processed.
	 * @return string Returns the processed string.
	 */
	function AddSlashes($data)
	{
		return mysql_real_escape_string($data, $this->link);
	}
}
?>