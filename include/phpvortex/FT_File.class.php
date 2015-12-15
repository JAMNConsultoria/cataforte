<?php
/**
 * File for class FT_File.
 *
 * @package Vortex
 * @subpackage DB
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/** Require the base class */
require_once('FT_Base.class.php');

/**
 * Database field, uploaded file stored in the FS.
 *
 * @package Vortex
 * @subpackage DB
 */
class FT_File extends FT_Base
{
	/**
	 * Where to move the uploaded file.
	 *
	 * @var string
	 */
	var $path = '.';

	/**
	 * Permission for the new file.
	 *
	 * @var int
	 */
	var $mode = 0664;

	/**
	 * Delete the old file if a new is uploaded?
	 *
	 * @var bool
	 */
	var $delete_old = TRUE;

	/**
	 * Rename file at upload?
	 *
	 * @var bool
	 */
	var $rename = FALSE;

	/**
	 * Name for the new uploaded file, you can use the following macros:
	 * %name% - Old file name, without extension.
	 * %ext% - Old file extension.
	 * %d% - day (01-31).
	 * %m% - month (01-12).
	 * %y% - year (1900-2999).
	 * %h% - hour (00-23).
	 * %i% - minute (00-59).
	 * %s% - second (00-59).
	 * %r% - random number (0-9999).
	 *
	 * @var string
	 */
	var $rename_mask = '%name%_%y%_%m%_%d%_%h%_%i%_%s%_%r%.%ext%';

	/**
	 * Overwrite file if exists?
	 * If file exists and overwrite is FALSE, the file is renamed.
	 *
	 * @var bool
	 */
	var $overwrite = FALSE;

	/**
	 * Allowed extensions, separated by ':', or empty if any.
	 *
	 * @var string
	 */
	var $allowed_extensions = '';

	/**
	 * Denied extensions, separated by ':', or empty if none.
	 *
	 * @var string
	 */
	var $denied_extensions = 'php:php3:php4:phtml';

	/**
	 * Output the field as a HTML Form.
	 *
	 * @param string $value Value to load the control with.
	 */
	function ShowForm($value, $origin = FT_OR_DB)
	{
		echo (empty($value)?'':"<font size='-2'>($value)</font> ")."<input type='file' name='{$this->name_form}' /><input type='hidden' name='{$this->name_form}_old_name' value='$value' />";
	}

	/**
	 * Output the field consistency testing in JavaScript.
	 */
	function JSConsist()
	{
		if ($this->required) {
			echo <<<END
	if (frm.{$this->name_form}.value == "") errors += " * {$this->label}\\n";

END;
		}
	}

	/**
	 * Extract the field from $vars, test the field consistency and return it ready for database insertion.
	 *
	 * @param array $vars Array containing the FORM data (usually $_POST).
	 * @return string Returns a string containing the parsed field data, or FALSE if the field is invalid.
	 */
	function Consist(&$vars)
	{
		if (!isset($_FILES[$this->name_form])) {
			if ($this->required) {
				return FALSE;
			} else {
				return "'".$this->default."'";
			}
		}
		$file =& $_FILES[$this->name_form];
		if (empty($file['name'])) return "'".$vars[$this->name_form.'_old_name']."'";
		$ext = substr(strrchr($file['name'], "."), 1);
		$fn = substr($file['name'], 0, -1 * (strlen($ext) + 1));
		if (!empty($this->allowed_extensions)) {
			$ae = explode(':', $this->allowed_extensions);
			if (!in_array($ext, $ae)) return FALSE;
		}
		if (!empty($this->denied_extensions)) {
			$de = explode(':', $this->denied_extensions);
			if (in_array($ext, $de)) return FALSE;
		}
		$path = realpath($this->path);
		if ($this->delete_old && !empty($var[$this->name_form.'_old_name']) && file_exists($path.DIRECTORY_SEPARATOR.$var[$this->name_form.'_old_name']))
			unlink($path.DIRECTORY_SEPARATOR.$var[$this->name_form.'_old_name']);
		$name = $file['name'];
		if ($this->rename || file_exists($path.DIRECTORY_SEPARATOR.$name) && !$this->overwrite) {
			$ra = array('%name%' => $fn, '%ext%' => $ext, '%r%' => rand(0, 9999));
			list($ra['%d%'], $ra['%m%'], $ra['%y%'], $ra['%h%'], $ra['%i%'], $ra['%s%']) = explode(':', date('d:m:Y:H:i:s'));
			$name = strtr($this->rename_mask, $ra);
			if (file_exists($path.DIRECTORY_SEPARATOR.$name) && !$this->overwrite) {
				if (strpos($this->rename_mask, '%r%') !== FALSE) {
					while (file_exists($path.DIRECTORY_SEPARATOR.$name)) {
						$ra = array('%name%' => $fn, '%ext%' => $ext, '%r%' => rand(0, 9999));
						list($ra['%d%'], $ra['%m%'], $ra['%y%'], $ra['%h%'], $ra['%i%'], $ra['%s%']) = explode(':', date('d:m:Y:H:i:s'));
						$name = strtr($this->rename_mask, $ra);
					}
				} else {
					return FALSE;
				}
			}
		}
		if (!move_uploaded_file($file['tmp_name'], $path.DIRECTORY_SEPARATOR.$name)) return FALSE;
		chmod($path.DIRECTORY_SEPARATOR.$name, $this->mode);
		if (!empty($GLOBALS['debug'])) {
			dv(1, 'PATH', $path);
			dv(1, 'NAME', $name);
		}
		return "'$name'";
	}
}

?>