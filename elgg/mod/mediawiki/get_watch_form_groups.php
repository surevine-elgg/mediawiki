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

// Load Mediawiki model
require_once(dirname(__FILE__) . "/models/model.php");

$page = get_input('page', '');

echo elgg_view('mediawiki/get_watch_form_groups', array('page' => $page));

?>
