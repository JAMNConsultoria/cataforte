<?php
/**
 * File for class FT_PKey.
 *
 * @package Vortex
 * @subpackage DB
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/** Require the base class */
require_once('FT_Hidden.class.php');

/**
 * Database field, auto-incrementing numeric Primary Key.
 *
 * @package Vortex
 * @subpackage DB
 */
class FT_PKey extends FT_Hidden
{
	/**
	 * Is the field a Primary Key?
	 *
	 * @var bool
	 */
	var $pkey = TRUE;

	/**
	 * Is the field required(obligatory)?
	 *
	 * @var bool
	 */
	var $required = TRUE;

	/**
	 * Default value of the field.
	 *
	 * @var string
	 */
	var $default = '-1';

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