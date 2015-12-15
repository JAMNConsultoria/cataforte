<?php
/**
 * File for class SEC_Edit.
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
 * Class for full Table editing, with forms and lists.
 *
 * @package Vortex
 * @subpackage Page
 */
class SEC_Edit extends SEC_Base
{
	/**
	 * Table to edit.
	 *
	 * @var TB_Base
	 */
	var $table = NULL;

	/**
	 * DB connection.
	 * @var DB_Base
	 */
	var $db = NULL;

	/**
	 * Specify a table class to instantiate.
	 *
	 * @var string
	 */
	var $table_class = '';

	/**
	 * Specify options to the $table_class instance.
	 *
	 * @var array
	 */
	var $table_opts = array();

	/**
	 * Form class to use.
	 *
	 * @var string
	 */
	var $form = 'SEC_Form';

	/**
	 * List class to use.
	 *
	 * @var string
	 */
	var $list = 'SEC_List';

	/**
	 * Navigator class to use. Empty to use no navigator.
	 *
	 * @var string
	 */
	var $navi = 'SEC_ListNavigator';

	/**
	 * Number of records per page to display in the list.
	 *
	 * @var int
	 */
	var $recspage = -1;

	/**
	 * Buttons to use, and order.
	 *
	 * The buttons are:<ul>
	 * <li /> 'S' = Submit
	 * <li /> 'R' = Reset
	 * <li /> 'N' = New
	 * <li /> 'D' = Delete
	 * <li /> 'F' = Find
	 * </ul>
	 * 
	 * @var string
	 */
	var $buttons = 'SRFND';

	/**
	 * Text for the submit button.
	 *
	 * @var string
	 */
	var $txt_submit = '';

	/**
	 * Image for the submit button.
	 *
	 * @var string
	 */
	var $img_submit = '';

	/**
	 * Text for the reset button.
	 *
	 * @var string
	 */
	var $txt_reset = '';

	/**
	 * Image for the reset button.
	 *
	 * @var string
	 */
	var $img_reset = '';

	/**
	 * Text for the new button.
	 *
	 * @var string
	 */
	var $txt_new = '';

	/**
	 * Image for the new button.
	 *
	 * @var string
	 */
	var $img_new = '';

	/**
	 * Text for the delete button.
	 *
	 * @var string
	 */
	var $txt_delete = '';

	/**
	 * Image for the delete button.
	 *
	 * @var string
	 */
	var $img_delete = '';

	/**
	 * Text for the find button.
	 *
	 * @var string
	 */
	var $txt_find = '';

	/**
	 * Image for the find button.
	 *
	 * @var string
	 */
	var $img_find = '';

	/**
	 * Constructor: Process form data and prepare the section to display.
	 *
	 * @param array $opts Parameters for the object, as 'var' => 'value'.
	 */
	function SEC_Edit($opts = array())
	{
		// Load all parameters into member variables.
		parent::SEC_Base($opts);

		// Load Table class if requested
		if (!empty($this->table_class)) {
			include_once($this->table_class.'.class.php');
			$this->table =& new $this->table_class($this->db, $this->table_opts);
		}

		// See what to do and perform the action
		if (!empty($_REQUEST["{$this->table->name}_action"])) {
			$this->Perform($_REQUEST["{$this->table->name}_action"]);
		}

		$submit = $this->BuildButtons();

		$url =& new URL();
		$url->parameters["{$this->table->name}_action"] = 'E';
		$opts = array(
			'table' => &$this->table,
			'url' => $url,
			'recspage' => $this->recspage
		);
		if (!empty($this->navi)) $opts['navigator'] = array(
			'class' => $this->navi,
			'opts' => array(
				'url' => $url
			)
		);
		if (isset($_SESSION['vortex_find_'.$this->table->name])) {
			$opts['data'] = $_SESSION['vortex_find_'.$this->table->name];
		}

		$this->AddSection("{$this->table->name}_list", $this->list, $opts);

		$url->parameters["{$this->table->name}_action"] = 'S';
		$this->AddSection("{$this->table->name}_form", $this->form, array(
			'table' => &$this->table,
			'url' => $url,
			'submit' => $submit
		));
	}

	/**
	 * Perform a action.
	 *
	 * @param string $action Action to perform ('S' for INSERT/UPDATE, 'E' for edit, 'N' for new, 'D' for DELETE, 'F' for find).
	 */
	function Perform($action)
	{
		global $vortex_errors;

		if (!strcmp($action, 'S')) {
			if ($this->table->Save($_REQUEST)) {
				$this->table->Seek();
			} else {
				switch ($this->table->Error()) {
				case TB_ERR_INCONSIST:
					$this->AddSection("{$this->table->name}_error", 'SEC_Static', array(
						'code' => "<div class='div_error'>{$vortex_errors[TB_ERR_INCONSIST]}</div>"
					));
					$this->table->LoadForm($_REQUEST, FT_OR_USER);
					break;
				default:
					trigger_error($vortex_errors[$this->table->Error()]);
					break;
				}
			}
		} elseif (!strcmp($action, 'E')) {
			$this->table->Seek($_REQUEST);
		} elseif (!strcmp($action, 'F')) {
			$this->table->Seek($_REQUEST);
			$_SESSION['vortex_find_'.$this->table->name] = array();
			foreach ($this->table->fields_form as $field) {
				if (isset($_REQUEST[$field->name_form]) && ($_REQUEST[$field->name_form] != $field->default)) $_SESSION['vortex_find_'.$this->table->name][$field->name_form] = $_REQUEST[$field->name_form];
			}
		} elseif (!strcmp($action, 'N')) {
			$this->table->Seek();
		} elseif (!strcmp($action, 'R')) {
			$this->table->Seek();
			unset($_SESSION['vortex_find_'.$this->table->name]);
		} elseif (!strcmp($action, 'D')) {
			if ($this->table->Delete($_REQUEST)) {
				$this->table->Seek();
			} else {
				switch ($this->table->Error()) {
				case TB_ERR_EMPTY:
					$this->AddSection("{$this->table->name}_error", 'SEC_Static', array(
						'code' => "<div class='div_error'>{$vortex_errors[TB_ERR_EMPTY]}</div>"
					));
					break;
				default:
					trigger_error($vortex_errors[$this->table->Error()]);
					break;
				}
			}
		}
	}

	/**
	 * Build the buttons and return them.
	 *
	 * @return string Returns the HTML code for the form's buttons.
	 */
	function BuildButtons()
	{
		global $vortex_msgs;

		$code = '';
		$buttons = preg_split('//', $this->buttons, -1, PREG_SPLIT_NO_EMPTY); 
		foreach ($buttons as $button) {
			if (!strcmp($button, 'S')) {
				if (empty($this->img_submit)) {
					$code .= "<input type='submit' value='".(empty($this->txt_submit)?$vortex_msgs['form_submit']:$this->txt_submit)."' />\n";
				} else {
					$code .= "<input type='image' src='{$this->img_submit}' alt='".(empty($this->txt_submit)?$vortex_msgs['form_submit']:$this->txt_submit)."' />\n";
				}
			} elseif (!strcmp($button, 'R')) {
				$url =& new URL();
				foreach ($this->table->fields as $field) if ($field->pkey) {
					unset($url->parameters[$field->name_db]);
				}
				unset($url->parameters["page_{$this->table->name}"]);
				$url->parameters["{$this->table->name}_action"] = 'R';
				$code .= "<script language='JavaScript'>\n\tfunction {$this->table->name}_Reset() {\n\t\tdocument.form_{$this->table->name}.action = '".$url->Get()."';\n\t\tdocument.form_{$this->table->name}.submit();\n\t}\n</script>\n";
				if (empty($this->img_reset)) {
					$code .= "<input type='button' value='".(empty($this->txt_reset)?$vortex_msgs['form_reset']:$this->txt_reset)."' onclick='{$this->table->name}_Reset();' />\n";
				} else {
					$code .= "<input type='image' src='{$this->img_reset}' alt='".(empty($this->txt_reset)?$vortex_msgs['form_reset']:$this->txt_reset)."' onclick='{$this->table->name}_Reset();' />\n";
				}
			} elseif (!strcmp($button, 'N')) {
				$url =& new URL();
				$url->parameters["{$this->table->name}_action"] = 'N';
				$code .= "<script language='JavaScript'>\n\tfunction {$this->table->name}_New() {\n\t\tdocument.form_{$this->table->name}.action = '".$url->Get()."';\n\t\tdocument.form_{$this->table->name}.submit();\n\t}\n</script>\n";
				if (empty($this->img_new)) {
					$code .= "<input type='button' value='".(empty($this->txt_new)?$vortex_msgs['form_new']:$this->txt_new)."' onclick='{$this->table->name}_New();' />\n";
				} else {
					$code .= "<input type='image' src='{$this->img_new}' alt='".(empty($this->txt_new)?$vortex_msgs['form_new']:$this->txt_new)."' onclick='{$this->table->name}_New();' />\n";
				}
			} elseif (!strcmp($button, 'D')) {
				$url =& new URL();
				$url->parameters["{$this->table->name}_action"] = 'D';
				$code .= "<script language='JavaScript'>\n\tfunction {$this->table->name}_Delete() {\n\t\tdocument.form_{$this->table->name}.action = '".$url->Get()."';\n\t\tdocument.form_{$this->table->name}.submit();\n\t}\n</script>\n";
				if (empty($this->img_delete)) {
					$code .= "<input type='button' value='".(empty($this->txt_delete)?$vortex_msgs['form_delete']:$this->txt_delete)."' onclick='{$this->table->name}_Delete();' />\n";
				} else {
					$code .= "<input type='image' src='{$this->img_delete}' alt='".(empty($this->txt_delete)?$vortex_msgs['form_delete']:$this->txt_delete)."' onclick='{$this->table->name}_Delete();' />\n";
				}
			} elseif (!strcmp($button, 'F')) {
				$url =& new URL();
				unset($url->parameters["page_{$this->table->name}"]);
				$url->parameters["{$this->table->name}_action"] = 'F';
				$code .= "<script language='JavaScript'>\n\tfunction {$this->table->name}_Find() {\n\t\tdocument.form_{$this->table->name}.action = '".$url->Get()."';\n\t\tdocument.form_{$this->table->name}.submit();\n\t}\n</script>\n";
				if (empty($this->img_find)) {
					$code .= "<input type='button' value='".(empty($this->txt_find)?$vortex_msgs['form_find']:$this->txt_find)."' onclick='{$this->table->name}_Find();' />\n";
				} else {
					$code .= "<input type='image' src='{$this->img_find}' alt='".(empty($this->txt_find)?$vortex_msgs['form_find']:$this->txt_find)."' onclick='{$this->table->name}_Find();' />\n";
				}
			}
		}
		return $code;
	}

	/**
	 * Outputs the section to the client.
	 */
	function Show()
	{
		echo "<div class='div_edit'>\n";
		parent::Show();
		echo "</div>\n";
	}
}

?>