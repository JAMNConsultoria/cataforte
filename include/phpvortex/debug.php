<?php
/**
 * Main file of the debug manager.
 *
 * @package Vortex
 * @subpackage Debug
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

isset($debug) or $debug = 0;
isset($_COOKIE['php_debug']) and $debug = intval($_COOKIE['php_debug']);
isset($_REQUEST['debug']) and setcookie('php_debug',(string)($debug = ($_REQUEST['debug'] != '')?intval($_REQUEST['debug']):1));
if ($debug) {
	/** Include external debug library. */
	include_once("debuglib.php");
	
	/**
	 * Outputs a debug message, and optionally a variable's content.
	 *
	 * @param string $msg Message to output.
	 * @param mixed $var Variable to be shown.
	 */
	function d($msg, $var = NULL) 
	{
		echo '<span>Debug: '.nl2br($msg).'</span><br>';
		if (!is_null($var)) {
			if (is_array($var)) {
				print_a($var, 0, TRUE);
			} else {
				print_a(array(gettype($var) => $var), 0, TRUE);
			}
		}
	}
	/**
	 * Same as d(), but first test the debug level.
	 * @see d
	 *
	 * @param int $lvl Required debug level for output.
	 * @param string $msg Message to output.
	 * @param mixed $var Variable to be shown.
	 */
	function dv($lvl, $msg, $var = NULL) // Only output if $debug >= $lvl
	{
		global $debug;
		if ($debug >= $lvl) {
			d($msg, $var);
		}
	}
} else {
	/** @ignore */
	function d($msg, $var = NULL) // Out of debug mode, does nothing.
	{
		return;
	}
	/** @ignore */
	function dv($lvl, $msg, $var = NULL) // Out of debug mode, does nothing.
	{
		return;
	}
}
?>