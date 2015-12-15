<?php
/**
 * File for class SEC_ListNavigator.
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
 * Class for Table lists navigators.
 *
 * @package Vortex
 * @subpackage Page
 */
class SEC_ListNavigator extends SEC_Base
{
	/**
	 * URL to link the page jumps to.
	 *
	 * @var URL
	 */
	var $url = NULL;

	/**
	 * Key to add the page number to into the URL.
	 *
	 * @var string
	 */
	var $key = 'page';

	/**
	 * Current page.
	 *
	 * @var int
	 */
	var $page = 0;

	/**
	 * Number of pages.
	 *
	 * @var int
	 */
	var $pages = 0;

	/**
	 * Output formatting: Go to first.
	 *
	 * @var string
	 */
	var $first = '&lt;&lt;';

	/**
	 * Output formatting: Go to previous.
	 *
	 * @var string
	 */
	var $previous = '&lt;';

	/**
	 * Output formatting: Go to next.
	 *
	 * @var string
	 */
	var $next = '&gt;';

	/**
	 * Output formatting: Go to last.
	 *
	 * @var string
	 */
	var $last = '&gt;&gt;';

	/**
	 * Output formatting: show $range pages before and after this one.
	 *
	 * @var int
	 */
	var $range = 3;

	/**
	 * Output formatting: show all pages if $pages <= $range_all.
	 *
	 * @var int
	 */
	var $range_all = 5;

	/**
	 * Outputs the section to the client.
	 *
	 * Outputs the HTML opening tags, then the content, then the HTML closing tags.
	 */
	function Show()
	{
		echo "<div class='div_navi'>\n";
		$start = max(0, $this->page - $this->range);
		$end = min($this->pages - 1, $this->page + $this->range);
		if ($this->pages <= $this->range_all) {
			$start = 0;
			$end = $this->pages - 1;
		}
		if ($this->page > 0) {
			$this->url->parameters[$this->key] = 0;
			echo "<a href='".$this->url->Get()."'>{$this->first}</a>";
			$this->url->parameters[$this->key] = $this->page - 1;
			echo "&nbsp;<a href='".$this->url->Get()."'>{$this->previous}</a>";
		} else {
			echo "{$this->first}&nbsp;{$this->previous}";
		}
		if ($start > 0) echo "&nbsp;...";
		for ($i = $start; $i <= $end; $i++) {
			$this->url->parameters[$this->key] = $i;
			if ($i != $this->page) {
				echo "&nbsp;<a href='".$this->url->Get()."'>".($i+1)."</a>";
			} else {
				echo "&nbsp;".($i+1);
			}
		}
		if ($end < $this->pages - 1) echo "&nbsp;...";
		if ($this->page < $this->pages - 1) {
			$this->url->parameters[$this->key] = $this->page + 1;
			echo "&nbsp;<a href='".$this->url->Get()."'>{$this->next}</a>";
			$this->url->parameters[$this->key] = $this->pages - 1;
			echo "&nbsp;<a href='".$this->url->Get()."'>{$this->last}</a><br>\n";
		} else {
			echo "&nbsp;{$this->next}&nbsp;{$this->last}<br>\n";
		}
		parent::Show();
		echo "</div>\n";
	}
}

?>