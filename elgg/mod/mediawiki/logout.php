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


// Load Elgg engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

// logout landing page with an invisible iframe logging the user out of Mediawiki
$mediawiki_url = get_plugin_setting('url', 'mediawiki');
$mediawiki_logout_url = $mediawiki_url . 'index.php?title=Special:UserLogout&amp;returnto=Main_Page';

$iframe = '<IFRAME id="logout_iframe" SRC="' . $mediawiki_logout_url . '" WIDTH="0" HIEGHT="0" FRAMEBORDER="0"></IFRAME>';

$title = elgg_echo('mediawiki:logout:title');

$body = '<div class="contentWrapper">' . elgg_echo('mediawiki:logout:description') . '</div>' . $iframe;

page_draw($title, elgg_view_layout("two_column_left_sidebar", '', elgg_view_title($title) . $body, elgg_view("account/forms/login")));

?>
