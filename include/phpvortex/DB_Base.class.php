<?php
/**
 * File for class DB_Base.
 *
 * @package Vortex
 * @subpackage DB
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/**
 * Base class for database connections.
 *
 * @package Vortex
 * @subpackage DB
 */
class DB_Base
{
	/**
	 * Server host.
	 * @var string
	 */
	var $server;

	/**
	 * Database.
	 * @var string
	 */
	var $db;

	/**
	 * Username.
	 * @var string
	 */
	var $user;

	/**
	 * Password.
	 * @var string
	 */
	var $pw;

	/**
	 * Connection with the database.
	 * @var mixed
	 */
	var $link;

	/**
	 * Constructor: Init the object and set the parameters.
	 *
	 * @param array $opts Parameters for the object, as 'var' => 'value'.
	 */
	function DB_Base($opts = array())
	{
		$this->link = NULL;
		foreach ($opts as $key => $value) {
			$this->$key = $value;
		}
	}

	/**
	 * Open a new connection to the database if not already connected.
	 *
	 * @abstract
	 * @param bool $persist Open a persistent connection?
	 * @return bool Returns TRUE if the connection was successfully established, FALSE if an error occurred.
	 */
	function Connect($persist=FALSE)
	{
		return FALSE;
	}

	/**
	 * Execute a query at the database.
	 *
	 * @abstract
	 * @param string $sql Query to run.
	 * @return RS_Base Returns a RecordSet object if there is a result to the query or TRUE if it was successfull, FALSE if a error occurred.
	 */
	function &Query($sql)
	{
		return FALSE;
	}

	/**
	 * Execute a SELECT query at the database.
	 *
	 * @param array $opts Parameters to the SELECT query, as 'option' => 'value'.
	 * @return RS_Base Returns a RecordSet object if there is a result to the query or FALSE if a error occurred.
	 */
	function &Select($opts = array())
	{
		$sel = "SELECT ";
		if (isset($opts['distinct'])) $sel .= "DISTINCT ";
		if (isset($opts['fields'])) {
			$fl = '';
			foreach ($opts['fields'] as $a => $f) if (is_int($a)) {
				$fl .= (strlen($fl)?', ':'') . $f;
			} else {
				$fl .= (strlen($fl)?', ':'') . $f . ' as ' . $a;
			}
			$sel .= $fl . ' ';
		} else {
			$sel .= "* ";
		}
		if (isset($opts['from'])) {
			$fl = '';
			foreach ($opts['from'] as $a => $f) if (is_int($a)) {
				$fl .= (strlen($fl)?', ':'') . $f;
			} else {
				$fl .= (strlen($fl)?', ':'') . $f . ' as ' . $a;
			}
			$sel .= "FROM $fl ";
		}
		if (isset($opts['where'])) $sel .= "WHERE {$opts['where']} ";
		if (isset($opts['group'])) $sel .= "GROUP BY {$opts['group']} ";
		if (isset($opts['having'])) $sel .= "HAVING {$opts['having']} ";
		if (isset($opts['order'])) $sel .= "ORDER BY {$opts['order']} ";
		if (!empty($GLOBALS['debug'])) dv(3, 'SELECT SQL', $sel);
		return $this->Query($sel);
	}

	/**
	 * Execute a INSERT INTO query at the database.
	 *
	 * @param string $table Table to insert into.
	 * @param array $values Fields and values to insert, as 'field' => 'value'.
	 * @return RS_Base Returns a RecordSet object if there is a result to the query or FALSE if a error occurred.
	 */
	function &Insert($table, $values)
	{
		$fl = '';
		$vl = '';
		foreach($values as $f => $v) {
			$fl .= (strlen($fl)?', ':'') . $f;
			$vl .= (strlen($vl)?', ':'') . $v;
		}
		$sql = "INSERT INTO $table ($fl) VALUES ($vl)";
		if (!empty($GLOBALS['debug'])) dv(3, 'INSERT SQL', $sql);
		return $this->Query($sql);
	}

	/**
	 * Execute a UPDATE query at the database.
	 *
	 * @param string $table Table to update.
	 * @param array $values Fields and values to update, as 'field' => 'value'.
	 * @param string $where Where clause to the query.
	 * @return RS_Base Returns a RecordSet object if there is a result to the query or FALSE if a error occurred.
	 */
	function &Update($table, $values, $where)
	{
		$sl = '';
		foreach($values as $f => $v) {
			$sl .= (strlen($sl)?', ':'') . "$f = $v";
		}
		$sql = "UPDATE $table SET $sl WHERE $where";
		if (!empty($GLOBALS['debug'])) dv(3, 'UPDATE SQL', $sql);
		return $this->Query($sql);
	}

	/**
	 * Execute a DELETE query at the database.
	 *
	 * @param string $table Table to update.
	 * @param string $where Where clause to the query.
	 * @return RS_Base Returns a RecordSet object if there is a result to the query or FALSE if a error occurred.
	 */
	function &Delete($table, $where)
	{
		$sql = "DELETE FROM $table WHERE $where";
		if (!empty($GLOBALS['debug'])) dv(3, 'DELETE SQL', $sql);
		return $this->Query($sql);
	}

	/**
	 * Close the connection to the database if still connected.
	 *
	 * @abstract
	 * @return bool Returns TRUE if the connection was closed, FALSE if it failed.
	 */
	function Close()
	{
		return FALSE;
	}

	/**
	 * Create a new connection with the same parameters and return it.
	 *
	 * @return DB_Base Return a new connection with the database, or FALSE if it fails.
	 */
	function &Duplicate()
	{
		$this_class = get_class($this);
		$new_db =& new $this_class(array('server' => $this->server, 'db' => $this->db, 'user' => $this->user, 'pw' => $this->pw));
		if ($new_db->Connect()) {
			return $new_db;
		}
		return FALSE;
	}

	/**
	 * Get the last error message from the connection.
	 *
	 * @abstract
	 * @return string Returns a string describing the last error that occurred in the connection.
	 */
	function Error()
	{
		return FALSE;
	}

	/**
	 * Transactions: Begin a new transaction.
	 *
	 * @abstract
	 * @return bool Returns TRUE if the new transaction began, FALSE if it failed.
	 */
	function Begin()
	{
		return FALSE;
	}

	/**
	 * Transactions: Commit a transaction.
	 *
	 * @abstract
	 * @return bool Returns TRUE if the transaction was commited, FALSE if it failed.
	 */
	function Commit()
	{
		return FALSE;
	}

	/**
	 * Transactions: Rollback a transaction.
	 *
	 * @abstract
	 * @return bool Returns TRUE if the transaction was cancelled, FALSE if it failed.
	 */
	function Rollback()
	{
		return FALSE;
	}

	/**
	 * Process a string for safe use in a database insertion.
	 *
	 * @abstract
	 * @param string $data The string to be processed.
	 * @return string Returns the processed string.
	 */
	function AddSlashes($data)
	{
		return $data;
	}
}
?>