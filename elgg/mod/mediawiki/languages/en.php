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


	$english = array(
		'item:object:mediawiki' => "Wiki",
		'mediawiki:watch_form:description' => "Here is a list of the groups that you own. You can "
			."add this page to the watch list for any of these groups by ticking the appropriate box.",
		//'mediawiki:watch_form:submit_message' => "... working ...",
		'mediawiki:watch_form:submit_message' => "The watch list for this page has been changed.",
		'mediawiki:watch_form:response_message' => "The watch list for this page has been changed.",
		'mediawiki:has_made_change' => "has changed the wiki page",
		'mediawiki:groupprofile' => "Wiki watch list",
		'mediawiki:url:settings:title' => "Wiki URL (must end with a slash)",
		'mediawiki:proxyurl:settings:title' => "Elgg proxy URL for JavaScript with subdomains (must end with a slash)",
		'mediawiki:digest_interval:settings:title' => "Digest interval in minutes (if defined, multiple edits by the same person on the same page will only be reported once during this interval)",
		'mediawiki:settings:group_profile_display:title' => "Group watch list display",
		'mediawiki:settings:group_profile_display_option:left' => "left column",
		'mediawiki:settings:group_profile_display_option:right' => "right column",
		'mediawiki:settings:group_profile_display_option:none' => "none",
		'mediawiki:enable_minor_edits' => "Show minor edits in wiki watch list",
		'mediawiki:logout:title' => "Log out",
		'mediawiki:logout:description' => "You are now logged-out.",
		'mediawiki:personal_options:description' => "Add this page to my personal watch list.",
		'mediawiki:num_display' => "Number of items to display",
		'mediawiki:widget_title' => "Wiki watch list",
		'mediawiki:widget:description' => "Show changes to pages on your personal wiki watch list.",
		'mediawiki:widget:setting:minor_edits:description' => "Show minor edits",
		'mediawiki:widget:setting:yes' => "yes",
		'mediawiki:widget:setting:no' => "no",
	);

	add_translation("en", $english);

