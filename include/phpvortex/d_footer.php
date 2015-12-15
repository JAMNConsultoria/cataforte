<?php
/**
 * Append file for debug.
 *
 * @package Vortex
 * @subpackage Debug
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

if ($debug) {
	if ($debug >= 2) {
		show_vars(1, 1, 0);
	} else {
		show_vars();
	}
}
?>