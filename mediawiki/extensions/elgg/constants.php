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


# Copyright (C) 2006 Aperto Elearning Solutions
# http://www.aperto-elearning.com/
#
# Modified significantly by Ed Lyons (June 2008)
# I added more comments, and two more constants
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
# http://www.gnu.org/copyleft/gpl.html


/*
  RUN_MODE is here so that you can control what happens when something
  goes wrong.  If you leave it at testing, users who try to do things
  you don't want to will get information about your setup that you
  might not want them to have
*/
#define('RUN_MODE', 'testing');
define('RUN_MODE', 'production');

/*
 You can use this to limit what domain you use for this integration:
 
 You might want to know why we have two variables here.  Well, if I
 just used the VALID_DOMAIN to www.yoursite.com - people might forget
 to change this setting, and wonder why it isn't working.  So if you
 want to do this, change it to true, then change the site name.
*/
define('CHECK_DOMAIN', false);
define('VALID_DOMAIN', 'www.example.com');

/* ELGG_MW_ELGG_AUTH_COOKIE_NAME: the name of the session
cookie set by Elgg.
*/
define('ELGG_MW_ELGG_AUTH_COOKIE_NAME', 'Elgg');

/* ELGG_PATH: the path relative to the host where Elgg is installed.
  The default value here is if MW is a subdirectory of your Elgg
  installation
*/

define('ELGG_PATH', '../elgg');
define('ELGG_URL', 'http://www.example.com/');



/* ELGG_MW_PUBLIC_ACTIONS: comma-delimited list of wiki actions that
can be performed without being logged in to Elgg. DO NOT INCLUDE SPACES.

If you want a walled garden, leave this as '' as that will mean that
the user cannot see anything without being logged in.

If you wanted the wiki to be readable without a login (probabaly more
common) then you might want:

define('ELGG_MW_PUBLIC_ACTIONS', 'view,history');
    
*/
define('ELGG_MW_PUBLIC_ACTIONS', '');

/* ELGG_MW_PUBLIC_PAGE_ACTIONS: an associative array of page titles and their
comma-delimited public actions. DO NOT INCLUDE SPACES. This takes precedence
over ELGG_MW_PUBLIC_ACTIONS.

I didn't fully understand this setting.  I wanted a walled garden, so I
made it accept nothing.  I think this means what pages you want to be available
no matter what happens - so you could do something like this and make sure
that at least the CSS shows....

array(
    'MediaWiki:Common.css'=>'view,raw',
    'MediaWiki:Earthblog.css'=>'view,raw',
    '-'=>'raw');

Note that the preceding example was what the original default was.

*/

$ELGG_MW_PUBLIC_PAGE_ACTIONS = array('' => '');



/* ELGG_MW_ELGG_WIKI_COOKIE_EXPIRY: specifies the number of seconds before
Wiki cookies expire. (This value will be written to MediaWiki's
$wgCookieExpiration global variable.) When these cookies expire, the plugin
will force an authentication check against the Elgg database. If the check
passes, the cookies are recreated automatically. */
define('ELGG_MW_ELGG_WIKI_COOKIE_EXPIRY', 3600);

/* ELGG_MW_ERR_AUTH_FAILURE: error message if the cookie is missing. */
define('ELGG_MW_ERR_AUTH_FAILURE', 'You must be logged in to perform this action.');

/* ELGG_MW_ERR_USER_NOT_FOUND: error message if the user was not found in
the Elgg database. */
define('ELGG_MW_ERR_USER_NOT_FOUND', 'The user does not exist in Elgg.');

/* ELGG_MW_ERR_USER_BANNED: error message if the user is banned from Elgg */
define('ELGG_MW_ERR_USER_BANNED', 'You have been banned.');

/* Elgg database info --------------------------- */
define('ELGG_DB_HOST', 'localhost');
define('ELGG_DB_NAME', 'elgg');
define('ELGG_DB_USER', 'user');
define('ELGG_DB_PASSWORD', 'password');
define('ELGG_DB_PREFIX', 'elgg');
?>
