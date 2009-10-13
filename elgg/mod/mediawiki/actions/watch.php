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
require_once(dirname(dirname(__FILE__)) . "/models/model.php");

$group_watchlist = get_input('group_watchlist', '');
$personal_watchlist = get_input('personal_watchlist', '');

$page = get_input('page', '');

mediawiki_set_group_watchlist($page, $group_watchlist);

if (is_array($personal_watchlist) && ($personal_watchlist[0] == 'yes'))
{
	$status = true;
}
else
{
	$status = false;
}

mediawiki_set_personal_watchlist($page, get_loggedin_userid(), $status);

echo elgg_echo('mediawiki:watch_form:response_message');

?>
