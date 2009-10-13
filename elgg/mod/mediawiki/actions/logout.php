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


// Log out
$result = logout();

// Set the system_message as appropriate

if ($result)
{
	system_message(elgg_echo('logoutok'));
}
else
{
	register_error(elgg_echo('logouterror'));
}

global $CONFIG;

forward($CONFIG->wwwroot . 'mod/mediawiki/logout.php');

?>
