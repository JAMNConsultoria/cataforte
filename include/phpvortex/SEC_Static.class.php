<?php
/**
 * File for class SEC_Static.
 *
 * @package Vortex
 * @subpackage Page
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/** Require the base class */
require_once('SEC_Base.class.php');

/**
 * Class for static page sections.
 *
 * @package Vortex
 * @subpackage Page
 */
class SEC_Static extends SEC_Base
{
	/**
	 * Optional file where the section is.
	 * @var string
	 */
	var $file;

	/**
	 * Optional HTML where the section is.
	 * @var string
	 */
	var $code;

	/**
	 * Outputs the section to the client.
	 *
	 * If a file was informed, output it. If some HTML code was informed, output it.
	 */
	function Show()
	{
		if (!is_null($this->file) && strcmp(trim($this->file), '')) {
			readfile($this->file);
		}
		if (!is_null($this->code) && strcmp(trim($this->code), '')) {
			echo $this->code;
		}
		parent::Show();
	}
}

?>