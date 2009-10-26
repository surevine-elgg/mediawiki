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


$mediawiki_url = $vars['mediawiki_url'];
$item          = $vars['item'];
$body          = '';

$body .= '<div class="river_item">';
$body .= '<div class="river_object">';
$body .= '<div class="river_object_mediawiki">';
$body .= '<div class="river_update">';
$body .= '<div class="river_object_mediawiki_update">';

$user  = get_entity($item->user_guid);

$body .= '<p><a href="' . $user->getUrl() . '">' . $user->name . '</a> ';
$body .= elgg_echo('mediawiki:has_made_change');
$body .= ' <a href="' . $mediawiki_url . 'index.php/' . $item->page . '"> ';
$body .= $item->page . '</a> ';

$body .= '<span class="river_item_time">(' . friendly_time($item->update_time) . ')</span></p>';
$body .= '</div>';
$body .= '</div>';
$body .= '</div>';
$body .= '</div>';
$body .= '</div>';

echo $body;

