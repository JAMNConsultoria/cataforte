<?php
/**
 * File for class APP_Base.
 *
 * @package Vortex
 * @subpackage Util
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/** Require the header for SEC_Page functionality. */
require_once('SEC_Page.class.php');

/**
 * Base class for applications.
 *
 * @package Vortex
 * @subpackage Util
 */
class APP_Base
{
	/**
	 * Class of DB connection.
	 * @var string
	 */
	var $connection_class = '';

	/**
	 * Parameters to the DB connection.
	 * @var array
	 */
	var $connection_opts = array();

	/**
	 * DB connection.
	 * @var DB_Base
	 */
	var $db = NULL;

	/**
	 * Array all the pages of an application.
	 *
	 * The $pages is an array containing arrays in the following form:
	 * <pre>
	 * 'page_name' => array(
	 * 						'class' => 'SEC_* Class',
	 * 						'opts' => array('Section options'),
	 * 					  [ 'title' => 'Page Title', ]
	 * 					  [ 'style' => array('Page style sheet',...), ]
	 * 					  [ 'layout' => array('page layout (see below)') ]
	 * ), ...
	 * </pre>
	 * If exists, the $db member variable is added to the 'opts' of the page.
	 * If 'title' is set, it is used instead of the default title.
	 * If 'style' is set, it is used instead of the default styles.
	 * If 'layout' is set, it is used instead of the default layout.
	 *
	 * @var array
	 */
	var $pages = array();

	/**
	 * Array with the default layout of the site.
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
	 * Array containing URLs to the default style sheets of the site.
	 *
	 * @var array
	 */
	var $styles = array();

	/**
	 * Default title to site pages.
	 *
	 * @var string
	 */
	var $title = '';

	/**
	 * Constructor: Load all parameters into member variables.
	 *
	 * @param array $opts Parameters for the object, as 'var' => 'value'.
	 */
	function APP_Base($opts = array())
	{
		$keys = array_keys($opts);
		foreach ($keys as $key) {
			$this->$key =& $opts[$key];
		}

		if (!empty($this->connection_class)) {
			$db_class = $this->connection_class;
			include_once($db_class.'.class.php');
			$this->db =& new $db_class($this->connection_opts);
		}
	}

	/**
	 * Outputs the page to the client.
	 *
	 * @param string $page Page to show.
	 */
	function Show($page)
	{
		if (empty($this->pages[$page])) {
			trigger_error("Page not found: $page");
			return;
		}
		if (isset($this->pages[$page]['layout'])) {
			$layout = $this->pages[$page]['layout'];
		} else {
			$layout = $this->layout;
		}
		if (isset($this->pages[$page]['style'])) {
			$style = $this->pages[$page]['style'];
		} else {
			$style = $this->styles;
		}
		if (isset($this->pages[$page]['title'])) {
			$title = $this->pages[$page]['title'];
		} else {
			$title = $this->title;
		}
		if (isset($this->pages[$page]['opts'])) {
			$opts = $this->pages[$page]['opts'];
		} else {
			$opts = array();
		}
		if ($this->db) {
			$this->db->Connect();
			$opts['db'] =& $this->db;
		}
		$pg =& new SEC_Page(array(
			'header' => array(
				'class' => 'SEC_Header',
				'opts' => array(
					'title' => $title,
					'styles' => $style
				)
			),
			'layout' => $layout,
			'content' => array(
				'class' => $this->pages[$page]['class'],
				'opts' => $opts
			)
		));
		$pg->Show();
		$this->db->Close();
	}
}

?>