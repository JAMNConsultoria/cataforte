<?php
/**
 * File for class SEC_Form.
 *
 * @package Vortex
 * @subpackage Page
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/** Require the base class */
require_once('SEC_Base.class.php');
/** Require the URL utility class */
require_once('URL.class.php');

/**
 * Class for Table form sections.
 *
 * @package Vortex
 * @subpackage Page
 */
class SEC_Form extends SEC_Base
{
	/**
	 * Table to show the form from.
	 *
	 * @var TB_Base
	 */
	var $table = NULL;

	/**
	 * URL of the form's action attribute.
	 *
	 * @var URL
	 */
	var $url = NULL;

	/**
	 * The line with the submit button/image and optionally some extra hidden fields and/or back buttons, etc.
	 *
	 * @var string
	 */
	var $submit = NULL;

	/**
	 * Array containing the data to seek as 'field' => 'value', used only in searches and the like.
	 *
	 * @var array
	 */
	var $data = NULL;

	/**
	 * Outputs the section to the client.
	 */
	function Show()
	{
		$this->table->ShowForm("form_{$this->table->name}", $this->url->Get(), $this->submit, $this->data);
		parent::Show();
	}
}

?>