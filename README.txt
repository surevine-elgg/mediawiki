/**
	* Elgg Mediawiki integration plugin
	*
	* @package Mediawiki
	* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	* @author Kevin Jardine <kevin.jardine@surevine.com>
	* @copyright Surevine Limited 2009
	* @link http://www.surevine.com/
*/
 
Requires: Elgg 1.5, Mediawiki 1.14, Curl PHP extension

The Mediawiki integration plugin uses the Elgg session cookie to integrate the
Mediawiki application into Elgg. This includes single sign on, a common Elgg
topbar and the ability to add tailored Mediawiki activity reports to Elgg group
profile pages, user profiles and dashboards.

Because the plugin uses the Elgg session cookie, both applications need to
have the same domain. It is possible to configure the integration to work 
across different subdomains (eg. elgg.example.com and wiki.example.com)
although this requires more configuration as described in the "Different 
subdomains" section below.

Installing the plugin in Elgg is simply a matter of unzipping it in the
mod directory, activating it in Tools administration, and adding a few
configuration settings.

Installing the integration on the Mediawiki side requires installing
four small extensions in the Mediawiki extensions directory and making some
small changes to CSS and the pageshell.


Elgg settings
-------------

Once activated, you can configure the plugin using the Mediawiki settings
link under Tools administration.

In most cases you will only need to enter your Mediawiki URL (must end in a
slash). You can also determine whether the Wiki watch list should appear on
the left or right side of group profiles.

The proxy URL setting is only for getting this plugin to work across different
subdomains as described in the "Different subdomains" section below. If both
Elgg and Mediawiki are using the same subdomain (eg. example.com/elgg and
example.com/wiki) then you will not need to use this.


Mediawiki settings
------------------

Unzip the four small Mediawiki extensions into the Mediawiki extensions
directory and move the set_topbar.php file into the Mediawiki root directory.

Set the constants in the elgg/constants.php directory. There are comments in
the file, but in a nutshell:

Replace:

define('RUN_MODE', 'testing');

with

define('RUN_MODE', 'production');

Set ELGGPATH to the Elgg file system path, eg.

define('ELGG_PATH', '../elgg');

set ELGGURL to the URL of your Elgg installation.

and
 
define('ELGG_DB_HOST', '');
define('ELGG_DB_NAME', '');
define('ELGG_DB_USER', '');
define('ELGG_DB_PASSWORD', '');
define('ELGG_DB_PREFIX', '');
 
to the appropriate values so that Mediawiki can consult your Elgg
database.

In addition, you will need to make some additions to the Mediawiki page
shell (eg. skins/Monobook.php).

Add:

<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('scriptpath' ) ?>/extensions/elgg/jquery/jquery-1.2.6.pack.js"><!-- jQuery main lib --></script>
<link rel="stylesheet" type="text/css" href="<?php echo ELGG_URL; ?>mod/mediawiki/mediawiki_css.php" />

into the header section of the page shell and

<div id="elggwiki_topbar"><?php if (isset($_SESSION['elgg_topbar'])) {echo $_SESSION['elgg_topbar'];} ?></div>
 
immediately after the body tag.

You will likely need to make some CSS changes to incorporate the Elgg topbar.

There is a modified copy of skins/monobook/main.css in the Mediawiki directory as
an example.

This also includes a section at the end to hide the Mediawiki login/logout links
as this will be done through Elgg.

Finally, to activate the Mediawiki extensions, add:

#Only logged-in users can edit
$wgGroupPermissions['*']['edit'] = false;

require_once('extensions/elgg/ElggAuthPlugin.php');
$wgAuth = new ElggAuthPlugin();

require_once('extensions/elgg_topbar/elgg_topbar.php');
wfElggTopbar();

require_once('extensions/elgg_notify/elgg_notify.php');
wfElggNotify();

require_once('extensions/elgg_watchlist/elgg_watchlist.php');
wfElggWatchlist();

to the end of LocalSettings.php

The $wgGroupPermissions makes sure that unlogged-in users can visit your
wiki but not edit anything.


Elgg logout landing page
------------------------

The Mediawiki plugin changes the Elgg logout procedure so that this displays
an explicit logout landing page rather than simply redirecting to the front
page as usual.

This landing page includes an invisible iframe that logs the user out of
Mediawiki as well.


Different subdomains
--------------------

It is possible to integrate Elgg and Mediawiki across different subdomains
(eg. elgg.example.com and wiki.example.com) with a bit more work.

To do this, make the following changes:

In php.ini set:

session.cookie_domain = ".example.com"

This should also work in Elgg's .htaccess as

phpvalue session.cookie_domain ".example.com"

This ensures that Elgg session cookies are visible across subdomains.

Then, in httpd.conf or a Mediawiki accessible .htaccess file, set a JavaScript
proxy URL as:

RewriteEngine On
RewriteRule /elggproxy/(.*) http://path-to-your-elgg/$1 [P]

This allows you to lie to JavaScript and convince it that it is
working in the same subdomain.

Make sure that you set ELGGURL in the elgg extension constants.php
file to:

http://path-to-your-wiki/elggproxy/

and put the same URL in the proxy URL setting for the Mediawiki plugin in Elgg.

This makes sure that JavaScript consistently uses the proxy URL rather
that the public one.

The proxy URL should never be visible so it should not affect your public URLs.


Known issues
------------

If you log out of Elgg and log in again as another user, Mediawiki logs out the
first user but sometimes doesn't detect the new user unless you clear the browser
cache (reported in Firefox 3).

The code for different subdomains has not been tested in configurations where the
subdomains are served by different web servers.

The code has not yet been deployed in a production environment and so should be
regarded as a beta release.

