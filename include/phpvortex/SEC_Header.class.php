<?php
/**
 * File for class SEC_Header.
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
 * Class for page headers.
 *
 * @package Vortex
 * @subpackage Page
 */
class SEC_Header extends SEC_Base
{
	/**
	 * Title of the page.
	 *
	 * @var string
	 */
	var $title = '';

	/**
	 * Array containing URLs to style sheets.
	 *
	 * @var array
	 */
	var $styles = array();

	/**
	 * Outputs the section to the client.
	 *
	 * Outputs the header opening tags, then the content, then the header closing tags.
	 */
	function Show()
	{
		echo "<head>\n";
		if (!empty($this->title)) echo "<title>{$this->title}</title>\n";
		if (!empty($this->styles)) {
			foreach ($this->styles as $style) echo "<link href='$style' rel='stylesheet' type='text/css' />\n";
		}
		parent::Show();
		echo "</head>\n";
	}
}

?>