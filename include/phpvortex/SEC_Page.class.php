<?php
/**
 * File for class SEC_Page.
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
 * Class for full page sections.
 *
 * @package Vortex
 * @subpackage Page
 */
class SEC_Page extends SEC_Base
{
	/**
	 * Array with the header of the page.
	 *
	 * The header is an array containing the section to use as header of the page in the following form:
	 * <pre>
	 * array( 'class' => 'Section Class', // Usually SEC_Header or a descendant
	 *        'opts' => array('Section options') );
	 * </pre>
	 * It is always sent before the BODY and all layout.
	 *
	 * @var array
	 */
	var $header = array();

	/**
	 * JavaScript to run on the onLoad event of the BODY tag.
	 *
	 * @var string
	 */
	var $onload = '';

	/**
	 * Array with the layout of the page.
	 *
	 * The layout is an array containing arrays in the following form:
	 * <pre>
	 * array( 'name' => 'Section Name',
	 *        'class' => 'Section Class',
	 *        'opts' => array('Section options') );
	 * </pre>
	 *
	 * @var array
	 */
	var $layout = array();

	/**
	 * Array with the content of the page.
	 *
	 * The content is an array containing the section to use as content of the page in the following form:
	 * <pre>
	 * array( 'class' => 'Section Class',
	 *        'opts' => array('Section options') );
	 * </pre>
	 * It is always inserted in the layout with the name 'content', replacing any placeholder there.
	 *
	 * @var array
	 */
	var $content = array();

	/**
	 * Constructor: Load all parameters into member variables.
	 *
	 * @param array $opts Parameters for the object, as 'var' => 'value'.
	 */
	function SEC_Page($opts = array())
	{
		parent::SEC_Base($opts);
		if (!empty($this->header)) {
			$this->AddSection('header', $this->header['class'], $this->header['opts']);
		}
		$this->AddSection('body_open', 'SEC_Static', array('code' => "<body>".(!empty($this->onload)?" onload='{$this->onload}'":'')."\n"));
		if (!empty($this->layout)) {
			foreach ($this->layout as $sect) {
				$this->AddSection($sect['name'], $sect['class'], $sect['opts']);
			}
		}
		if (!empty($this->content)) {
			$this->AddSection('content', $this->content['class'], $this->content['opts']);
		}
		$this->AddSection('body_close', 'SEC_Static', array('code' => "</body>\n"));
	}

	/**
	 * Outputs the section to the client.
	 *
	 * Outputs the HTML opening tags, then the content, then the HTML closing tags.
	 */
	function Show()
	{
		echo "<html>\n";
		parent::Show();
		echo "</html>";
	}
}

?>