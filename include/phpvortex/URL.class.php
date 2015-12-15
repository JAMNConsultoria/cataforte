<?php
/**
 * File for class URL.
 *
 * @package Vortex
 * @subpackage Util
 * @author Thiago Ramon Gonçalves Montoya
 * @copyright Copyright 2004, Thiago Ramon Gonçalves Montoya
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 */

/**
 * Utility class to help the use of URLs.
 *
 * @package Vortex
 * @subpackage Util
 */
class URL
{
	/**
	 * Base URL (everything before the ?).
	 *
	 * @var string
	 */
	var $base;

	/**
	 * URL Parameters (everything after the ?) as 'name' => 'value'.
	 *
	 * @var array
	 */
	var $parameters;

	/**
	 * Constructor: Create a new URL object with a URL, using the present page as default.
	 */
	function URL($url = '')
	{
		if (empty($url)) {
			$this->Parse($_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING'])?('?'.$_SERVER['QUERY_STRING']):''));
		} else {
			$this->Parse($url);
		}
	}

	/**
	 * Parse a URL into it's parts.
	 *
	 * @param string $url URL to parse.
	 */
	function Parse($url)
	{
		$parts = explode('?',$url);
		$this->base = $parts[0];
		$this->parameters = array();
		if (count($parts)==2) {
			$params = explode('&', $parts[1]);
			foreach ($params as $param) {
				$pieces = explode('=',$param);
				if (count($pieces)==2) $this->parameters[$pieces[0]]  = $pieces[1];
			}
		}
	}

	/**
	 * Gets the full URL.
	 *
	 * @return string Returns the full URL.
	 */
	function Get()
	{
		$url = $this->base;
		if (!empty($this->parameters)) {
			$pieces = array();
			foreach ($this->parameters as $key => $value) $pieces[] = "$key=$value";
			$url .= '?'.implode('&amp;',$pieces);
		}
		return $url;
	}
}

?>