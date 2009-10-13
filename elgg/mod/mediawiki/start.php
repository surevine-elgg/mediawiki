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


function mediawiki_init()
{
	// Load system configuration
	global $CONFIG;
	
	// Load the language files
	register_translations($CONFIG->pluginspath . "mediawiki/languages/");
	
	add_group_tool_option('mediawiki_minor_edits', elgg_echo('mediawiki:enable_minor_edits'), false);
	
	// add to the css
	extend_view('css', 'mediawiki/css');
	
	// Set up menu for users
	if (isloggedin())
	{
		$mediawiki_url = get_plugin_setting('url', 'mediawiki');
		add_menu(elgg_echo('item:object:mediawiki'), $mediawiki_url . "index.php");
	}
	
	// add a widget
	add_widget_type('mediawiki', elgg_echo("mediawiki:widget_title"), elgg_echo('mediawiki:widget:description'));
}


function mediawiki_pagesetup()
{
	// add to group profile page
	$page_owner = page_owner_entity();
	
	if ($page_owner instanceof ElggGroup && get_context() == 'groups')
	{
		$group_watchlist = get_plugin_setting('group_watchlist', 'mediawiki');

		if (!$group_watchlist || $group_watchlist != 'no')
		{
			$group_profile_display = get_plugin_setting('group_profile_display', 'mediawiki');

			if (!$group_profile_display || $group_profile_display == 'left')
			{
				extend_view('groups/left_column', 'mediawiki/watchlist');
			}
			else if ($group_profile_display == 'right')
			{
				extend_view('groups/right_column', 'mediawiki/watchlist');
			}
		}
	}
}

register_elgg_event_handler('init', 'system', 'mediawiki_init');
register_elgg_event_handler('pagesetup', 'system', 'mediawiki_pagesetup');

global $CONFIG;
register_action("mediawiki/watch", false, $CONFIG->pluginspath . "mediawiki/actions/watch.php");
register_action("logout", false, $CONFIG->pluginspath . "mediawiki/actions/logout.php");

?>
