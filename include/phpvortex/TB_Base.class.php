<?php
/**
 * File for class TB_Base.
 *
 * @package Vortex
 * @subpackage DB
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/** Require the global configuration file for access to the localized messages */
require_once('conf/conf.php');

/**
 * Base class for tables in databases.
 *
 * @package Vortex
 * @subpackage DB
 */
class TB_Base
{
	/**
	 * Database where the table is.
	 * @var DB_Base
	 */
	var $db;

	/**
	 * Table name.
	 * @var string
	 */
	var $name;

	/**
	 * Table name in the database.
	 * Default = {@link $name}
	 *
	 * @var string
	 */
	var $name_db;

	/**
	 * Label of the table for forms and listings.
	 * Default = {@link $name}
	 *
	 * @var string
	 */
	var $label;

	/**
	 * Last error in the object.
	 * @var int
	 */
	var $error = TB_ERR_OK;

	/**
	 * Array containing all the fields of the table (FT_* classes).
	 * @var array
	 */
	var $fields = array();

	/**
	 * Array containing references to the fields to use in lists.
	 * @var array
	 */
	var $fields_list = array();

	/**
	 * Order by parameter used in lists.
	 * @var string
	 */
	var $list_order = '';

	/**
	 * Number of records per page to display in lists.
	 * @var int
	 */
	var $list_recspage = 15;

	/**
	 * Array containing references to the fields to use in forms.
	 * @var array
	 */
	var $fields_form = array();

	/**
	 * Array with the current row of the table, for output, edit or search.
	 * @var array
	 */
	var $data = array();

	/**
	 * Origin of $data.
	 * @var int
	 */
	var $data_origin = FT_OR_DB;

	/**
	 * Constructor: Init the object, and define the table's fields and relationships.
	 *
	 * @param DB_Base $db Database where the table is.
	 * @param array $opts Parameters for the object, as 'var' => 'value'.
	 */
	function TB_Base(&$db, $opts = array())
	{
		$keys = array_keys($opts);
		foreach ($keys as $key) {
			$this->$key =& $opts[$key];
		}

		$this->db =& $db;
		is_null($this->name_db) and $this->name_db = $this->name;
		is_null($this->label) and $this->label = $this->name;
	}

	/**
	 * Get the last error message.
	 *
	 * @return string Returns the last error message, or FALSE if no error.
	 */
	function Error()
	{
		if ($this->error == TB_ERR_OK) return FALSE;
		return $this->error;
	}

	/**
	 * Set the internal buffer to a record to show/edit, or a blank one.
	 *
	 * @param array $data Array containing the data to seek as 'field' => 'value'.
	 * @param bool $pkonly Use only the pkey's in the search?
	 * @return bool Returns TRUE on success, FALSE on error.
	 */
	function Seek($data = NULL, $pkonly = TRUE)
	{
		if (empty($data)) {
			$this->data = array();
			return TRUE;
		}
		$where = '';
		foreach ($this->fields as $field) if (!$pkonly || $field->pkey) {
			if (isset($data[$field->name_form])) $where .= (strlen($where)?' AND ':'').$field->Where($data[$field->name_form]);
		}
		if (!strlen($where)) {
			$this->data = array();
			$this->error = TB_ERR_NOPKEY;
			return FALSE;
		}
		if (!($rs = $this->db->Select(array('from' => array($this->name_db), 'where' => $where)))) {
			$this->data = array();
			$this->error = TB_ERR_DB;
			return FALSE;
		}
		if (($this->data = $rs->Row()) === FALSE) {
			$this->data = array();
			$this->error = TB_ERR_EMPTY;
			return FALSE;
		}
		$rs->Close();
		return TRUE;
	}

	/**
	 * Set the internal buffer to the data coming from a form.
	 *
	 * @param array $data Array containing the data to load as 'field' => 'value'.
	 */
	function LoadForm($data, $origin = FT_OR_DB)
	{
		$this->data = array();
		foreach ($this->fields as $field) {
			if (isset($data[$field->name_form])) $this->data[$field->name_db] = $data[$field->name_form];
		}
		$this->data_origin = $origin;
	}

	/**
	 * INSERT or UPDATE the data to the database.
	 *
	 * @param array $data Array containing all the data to save as 'field' => 'value'.
	 * @return bool Returns TRUE on success, FALSE on error.
	 */
	function Save($data)
	{
		if (empty($data)) {
			$this->error = TB_ERR_EMPTY;
			return FALSE;
		}
		$values = array();
		$where = '';
		foreach ($this->fields as $field) {
			if (($value = $field->Consist($data)) === FALSE) {
				if (!empty($GLOBALS['debug'])) {
					dv(1, 'INCONSISTENT FIELD', $field);
					dv(1, 'DATA', $data);
				}
				$this->error = TB_ERR_INCONSIST;
				return FALSE;
			}
			if ($field->pkey) {
				$where .= (strlen($where)?' AND ':'').$field->Where($value);
			} else {
				$values[$field->name_db] = $value;
			}
		}
		if (empty($where)) {
			if (empty($values)) {
				$this->error = TB_ERR_EMPTY;
				return FALSE;
			}
			if (!$this->db->Insert($this->name_db, $values)) {
				$this->error = TB_ERR_DB;
				return FALSE;
			}
			return TRUE;
		}
		if (empty($values)) {
			$this->error = TB_ERR_EMPTY;
			return FALSE;
		}
		if (!($rs = $this->db->Select(array('fields' => array('cnt' => 'COUNT(*)'), 'from' => array($this->name_db), 'where' => $where)))) {
			$this->error = TB_ERR_DB;
			return FALSE;
		}
		if (($row = $rs->Row()) === FALSE) {
			$this->error = TB_ERR_DB;
			return FALSE;
		}
		$rs->Close();
		if ($row['cnt'] > 0) {
			if (!$this->db->Update($this->name_db, $values, $where)) {
				$this->error = TB_ERR_DB;
				return FALSE;
			}
		} else {
			if (!($rs =& $this->db->Insert($this->name_db, $values))) {
				$this->error = TB_ERR_DB;
				return FALSE;
			}
			$id = $rs->LastId();
			return (($id > 0)?$id:TRUE);
		}
		return TRUE;
	}

	/**
	 * Delete a record from the table.
	 *
	 * @param array $data Array containing the primary key(s) to the table as 'field' => 'value', or NULL to delete the current record.
	 * @return bool Returns TRUE on success, FALSE on error.
	 */
	function Delete($data = NULL)
	{
		if (!empty($data)) if (!$this->Seek($data)) return FALSE;
		if (empty($this->data)) {
			$this->error = TB_ERR_EMPTY;
			return FALSE;
		}
		$where = '';
		foreach ($this->fields as $field) if ($field->pkey) {
			if (isset($this->data[$field->name_db])) $where .= (strlen($where)?' AND ':'').$field->Where($this->data[$field->name_db]);
		}
		if (!strlen($where)) {
			$this->error = TB_ERR_NOPKEY;
			return FALSE;
		}
		if (!$this->db->Delete($this->name_db, $where)) {
			$this->error = TB_ERR_DB;
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Outputs a list for viewing/editing records.
	 *
	 * @param URL $url URL to link the records to. Leave blank for no links.
	 * @param int $page Current page to show the records. Leave -1 to show all records.
	 * @param array $data Array containing the data to seek as 'field' => 'value'.
	 * @return bool Returns TRUE on success, FALSE on error.
	 */
	function ShowList($url = NULL, $page = -1, $data = NULL)
	{
		$sel = array(
			'from' => array($this->name_db)
		);
		foreach ($this->fields_list as $field) {
			$sel['fields'][] = $field->name_db;
		}
		foreach ($this->fields as $field) if ($field->pkey) {
			$sel['fields'][] = $field->name_db;
		}
		if (!empty($data)) {
			$where = '';
			foreach ($this->fields as $field) {
				if (isset($data[$field->name_form])) $where .= (strlen($where)?' AND ':'').$field->Where($data[$field->name_form]);
			}
			if (strlen($where)) $sel['where'] = $where;
		}
		if (!empty($this->list_order)) $sel['order'] = $this->list_order;
		if (!($rs = $this->db->Select($sel))) {
			$this->error = TB_ERR_DB;
			return FALSE;
		}
		if ($page >= 0) {
			if ($page > 0) $rs->SetRow($this->list_recspage * $page);
			$rows = array();
			for ($i = 0; $i < $this->list_recspage; $i++) {
				if (!($rows[] = $rs->Row())) break;
			}
		} else {
			if (!($rows = $rs->All())) {
				$this->error = TB_ERR_EMPTY;
				return FALSE;
			}
		}
		echo "<div class='div_list'><table id='tb_{$this->name}'>\n<thead><tr>\n";
		foreach ($this->fields_list as $field) {
			echo "<th class='fd_{$field->name}'>{$field->label}</th>\n";
		}
		echo "</tr></thead>\n<tbody>\n";
		foreach ($rows as $row) if (!empty($row)) {
			echo "<tr>\n";
			if (!empty($url)) {
				foreach ($this->fields as $field) if ($field->pkey) {
					$url->parameters[$field->name_db] = $row[$field->name_db];
				}
			}
			foreach ($this->fields_list as $field) {
				echo "<td class='fd_{$field->name}'>";
				if (!empty($url)) echo "<a href='".$url->Get()."'>";
				$field->ShowPlain($row[$field->name_db]);
				if (!empty($url)) echo '</a>';
				echo "</td>\n";
			}
			echo "</tr>\n";
		}
		echo "</tbody>\n</table></div>\n";
		return TRUE;
	}

	/**
	 * Gets the number of pages of a list.
	 *
	 * @param array $data Array containing the data to seek as 'field' => 'value'.
	 * @return int Returns the number of pages in a list or FALSE on error.
	 */
	function NumPages($data = NULL)
	{
		$sel = array(
			'fields' => array('cnt' => 'COUNT(*)'),
			'from' => array($this->name_db)
		);
		if (!empty($data)) {
			$where = '';
			foreach ($this->fields as $field) {
				if (isset($data[$field->name_form])) $where .= (strlen($where)?' AND ':'').$field->Where($data[$field->name_form]);
			}
			if (strlen($where)) $sel['where'] = $where;
		}
		if (!($rs = $this->db->Select($sel))) {
			$this->error = TB_ERR_DB;
			return FALSE;
		}
		if (($row = $rs->Row()) === FALSE) {
			$this->error = TB_ERR_DB;
			return FALSE;
		}
		$rs->Close();
		return (int)ceil($row['cnt'] / $this->list_recspage);
	}

	/**
	 * Outputs a form for inserting/editing records.
	 *
	 * @param string $name The HTML FORM name.
	 * @param string $action Where to submit the data to.
	 * @param string $submit HTML containing the last line in a form, leave NULL to use a normal submit button.
	 * @param array $data Array containing the data to seek as 'field' => 'value'.
	 * @return bool Returns TRUE on success, FALSE on error.
	 */
	function ShowForm($name, $action, $submit = NULL, $data = NULL)
	{
		global $vortex_msgs;
		if (!empty($data)) $this->Seek($data);
		echo <<<END
<div class="div_form">
<script language="javascript">
function Submit_{$name}(frm)
{
	var errors = '';


END;
		foreach ($this->fields_form as $field) {
			$field->JSConsist();
		}
		echo <<<END

	if (errors != '') {
		alert("{$vortex_msgs['consist_error']}\\n\\n"+errors);
		return false;
	}
	return true;
}
</script>
<table>
<form name="{$name}" action="{$action}" method="post" enctype="multipart/form-data" onSubmit="return Submit_{$name}(this);">
<tbody>
END;
		foreach ($this->fields as $field) if ($field->pkey) {
			$field->Show($this->data, TRUE, $this->data_origin);
		}
		foreach ($this->fields_form as $field) {
			$field->Show($this->data, TRUE, $this->data_origin);
		}
		echo "<tr class='submit' id='submit_$name'><td colspan='2'>".(empty($submit)?"<input type='submit' />&nbsp;<input type='reset' />":$submit)."</td></tr>";
		echo "</tbody>\n</form>\n</table></div>\n";
	}
}

?>