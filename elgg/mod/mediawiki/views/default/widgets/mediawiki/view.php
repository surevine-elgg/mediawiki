<?php

/**
	* Elgg Mediawiki integration plugin
	*
	* @package Mediawiki
	* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	* @author Kevin Jardine <kevin.jardine@surevine.com>
	* @copyright Surevine Limited 2009
	* @link http://www.surevine.com/
*/


// Load Mediawiki model
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/models/model.php");

// the number of watch items to display
$num = (int) $vars['entity']->num_display;

if (!$num)
{
	$num = 5;
}

if ($vars['entity']->minor_edits == 'yes')
{
	$minor = true;
}
else
{
	$minor = false;
}

$mediawiki_url = get_plugin_setting('url', 'mediawiki');

// the page owner
$owner = get_entity($vars['entity']->owner_guid);

// Get the watched items

$watchlist = mediawiki_get_watched_edits($owner, $num, $minor);

// If there are any edits to view, view them
if (is_array($watchlist) && sizeof($watchlist) > 0)
{
	$body .= '<div class="contentWrapper">';
	$body .= '<div class="river_item_list">';

	foreach ($watchlist as $item)
	{
		$body .= elgg_view('mediawiki/watch_item', array('mediawiki_url' => $mediawiki_url, 'item' => $item));
	}

	$body .= '</div>';
	$body .= '</div>';
	
	echo $body;
}

?>
