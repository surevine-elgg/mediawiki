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
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/models/model.php");

$body = '';
$mediawiki_url = get_plugin_setting('url', 'mediawiki');

if ($vars['entity']->mediawiki_minor_edits_enable == "yes")
{
	$minor = TRUE;
}
else
{
	$minor = FALSE;
}

$watchlist = mediawiki_get_watched_edits($vars['entity'], 10, $minor);

if ($watchlist)
{
	$body .= '<div id="group_pages_widget">';
	$body .= '<h2>' . elgg_echo("mediawiki:groupprofile") . '</h2>';
	$body .= '<div class="contentWrapper">';
	$body .= '<div class="river_item_list">';

	foreach ($watchlist as $item)
	{
		$body .= elgg_view('mediawiki/watch_item',
							array('mediawiki_url' => $mediawiki_url, 'item' => $item));
	}

	$body .= '</div>';
	$body .= '</div>';
	$body .= '</div>';
}

echo $body;

