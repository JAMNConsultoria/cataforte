<?php
/**
 * File for class DB_MSSQL.
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
require_once('RS_MSSQL.class.php');

/**
 * Class for MS SQL Server database connection.
 *
 * @package Vortex
 * @subpackage DB
 */
class DB_MSSQL extends DB_Base
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
			$this->link = mssql_pconnect($this->server, $this->user, $this->pw);
		} else {
			$this->link = mssql_connect($this->server, $this->user, $this->pw);
		}
		if ($this->link !== FALSE) {
			if (mssql_select_db($this->db, $this->link)) {
				return TRUE;
			}
			mssql_close($this->link);
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
		$rs = mssql_query($sql,$this->link);
		if ($rs === FALSE) {
			return FALSE;
		}
		return new RS_MSSQL($this, $rs);
	}

	/**
	 * Close the connection to the database if still connected.
	 *
	 * @return bool Returns TRUE if the connection was closed, FALSE if it failed.
	 */
	function Close()
	{
		if (mssql_close($this->link)) {
			$this->link = NULL;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Transactions: Begin a new transaction.
	 *
	 * @return bool Returns TRUE if the new transaction began, FALSE if it failed.
	 */
	function Begin()
	{
		return $this->Query('BEGIN TRANSACTION');
	}

	/**
	 * Transactions: Commit a transaction.
	 *
	 * @return bool Returns TRUE if the transaction was commited, FALSE if it failed.
	 */
	function Commit()
	{
		return $this->Query('COMMIT TRANSACTION');
	}

	/**
	 * Transactions: Rollback a transaction.
	 *
	 * @return bool Returns TRUE if the transaction was cancelled, FALSE if it failed.
	 */
	function Rollback()
	{
		return $this->Query('ROLLBACK TRANSACTION');
	}

	/**
	 * Process a string for safe use in a database insertion.
	 *
	 * @param string $data The string to be processed.
	 * @return string Returns the processed string.
	 */
	function AddSlashes($data)
	{
		return addslashes($data);
	}
}
?>