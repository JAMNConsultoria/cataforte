<?php
/**
 * File for class SEC_Include.
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
 * Class for include() sections.
 *
 * @package Vortex
 * @subpackage Page
 */
class SEC_Include extends SEC_Base
{
	/**
	 * File to include.
	 * @var string
	 */
	var $include;

	/**
	 * Outputs the section to the client.
	 */
	function Show()
	{
		if (!is_null($this->include) && strcmp(trim($this->include), '')) {
			include($this->include);
		}
		parent::Show();
	}
}

?>