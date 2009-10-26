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

$default_css = elgg_view("mediawiki/topbar_css");

header("Content-type: text/css", TRUE);
header('Expires: ' . date('r', time() + 864000), TRUE);
header("Pragma: public", TRUE);
header("Cache-Control: public", TRUE);
header("Content-Length: " . strlen($default_css));

echo $default_css;

