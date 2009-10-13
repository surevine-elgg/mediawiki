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


# Initialise common code
$preIP = dirname(__FILE__);
require_once("$preIP/includes/WebStart.php");

# Initialize MediaWiki base class
require_once("$preIP/includes/Wiki.php");
$mediaWiki = new MediaWiki();

$_SESSION['elgg_topbar'] = $_POST['elgg_topbar'];

?>
