<?php
/**
 * File for class SEC_List.
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
 * Class for Table lists sections.
 *
 * @package Vortex
 * @subpackage Page
 */
class SEC_List extends SEC_Base
{
	/**
	 * Table to show the list from.
	 *
	 * @var TB_Base
	 */
	var $table = NULL;

	/**
	 * URL to link the table items to.
	 *
	 * @var URL
	 */
	var $url = NULL;

	/**
	 * Array with the navigator of the list.
	 *
	 * The navigator is an array containing the section to use as navigator of the page in the following form:
	 * <pre>
	 * array( 'class' => 'Section Class', // Usually SEC_ListNavigator or a descendant
	 *        'opts' => array('Section options') );
	 * </pre>
	 * It is always sent after the list.
	 *
	 * @var array
	 */
	var $navigator = array();

	/**
	 * Number of records per page to display.
	 *
	 * @var int
	 */
	var $recspage = -1;

	/**
	 * Array containing the data to seek as 'field' => 'value', used only in searches and the like.
	 *
	 * @var array
	 */
	var $data = NULL;

	/**
	 * Constructor: Load all parameters into member variables.
	 *
	 * @param array $opts Parameters for the object, as 'var' => 'value'.
	 */
	function SEC_List($opts = array())
	{
		parent::SEC_Base($opts);
		if (!empty($this->navigator)) {
			if (empty($this->navigator['opts']['key'])) $this->navigator['opts']['key'] = "page_{$this->table->name}";
			$this->AddSection("{$this->table->name}_list_navigator", $this->navigator['class'], $this->navigator['opts']);
		}
	}

	/**
	 * Outputs the section to the client.
	 */
	function Show()
	{
		echo "<div class='div_list_full'>\n";
		$page = (isset($_REQUEST["page_{$this->table->name}"])?$_REQUEST["page_{$this->table->name}"]:0);
		if ($this->recspage > 0) $this->table->list_recspage = $this->recspage;
		$this->table->ShowList($this->url, $page, $this->data);
		if (isset($this->sections["{$this->table->name}_list_navigator"])) {
			$this->sections["{$this->table->name}_list_navigator"]->page = $page;
			$this->sections["{$this->table->name}_list_navigator"]->pages = $this->table->NumPages($this->data);
		}
		parent::Show();
		echo "</div>\n";
	}
}

?>