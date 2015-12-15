<?php
/**
 * General configuration file for PHP Vortex with system-wide defaults.
 *
 * @package Vortex
 * @subpackage Util
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/**
 * No Error.
 *
 * Error codes for TB_*
 */
define('TB_ERR_OK', 0);
/**
 * Inconsistency detected in a field.
 *
 * Error codes for TB_*
 */
define('TB_ERR_INCONSIST', 1);
/**
 * No Primary Key found.
 *
 * Error codes for TB_*
 */
define('TB_ERR_NOPKEY', 2);
/**
 * Empty parameter or RecordSet.
 *
 * Error codes for TB_*
 */
define('TB_ERR_EMPTY', 3);
/**
 * Database error (use {@link DB_Base::Error()} to discover which error).
 *
 * Error codes for TB_*
 */
define('TB_ERR_DB', 4);
/**
 * Field origin is from DataBase.
 * Used by {@link FT_Base::ShowForm()} and {@link FT_Base::ShowPlain()}.
 */
define('FT_OR_DB', 1);
/**
 * Field origin is from User.
 * Used by {@link FT_Base::ShowForm()} and {@link FT_Base::ShowPlain()}.
 */
define('FT_OR_USER', 2);

/** require one of the localized message files */
require_once('lang_en.php');
// require_once('lang_pt_br.php');

?>