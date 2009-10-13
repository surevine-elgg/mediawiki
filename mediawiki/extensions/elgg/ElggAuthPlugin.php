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

/**
 * Altered for Elgg 1.5 by:
 * Kevin Jardine <kevin.jardine@surevine.com>
 * Surevine Limited
 * http://www.surevine.com/

/**
 * This is a rewrite of the original ElggAuth Plugin for Media Wiki
 * written by the Aperto guys.  It is for MediaWiki 1.12 and Elgg
 * 0.92.  It was written by Ed Lyons (ejlyons@ix.netcom.com)
 *
 *  Altered significantly by Ed Lyons (June 2008)
 *  1. Got rid of deprecated calls
 *  2. Changed access control for methods
 *  3. Fixed errors in require paths
 *  4. Better error handling and production-appropriate error messages
 *  5. Some other stuff
 *
 * This is *not* a plugin for Elgg, but an extension for MediaWiki
 * in order for it to use Elgg's authentication mechanism.
 *
 * HOW THIS WORKS:
 *
 * You, in constants.php, determine the behavior of the authentication.
 * You really have to make sure all of those parameters are correct, OK?
 *
 * The important thing is what the relationship you want between the two
 * platforms.
 *
 * The intent of this plugin is that MediaWiki is a slave to Elgg, not that
 * MediaWiki has a completely independent existence.  So this is not a pure
 * single sign-on situation.  Why not?  Because the model here is that
 * when a legit Elgg user gets to this wiki, and he doesn't have an account
 * on it, this script will create his account and then use it from now on.
 *
 * Here are the possible main scenarios:
 *
 * 1. You have a walled garden. Nobody can see your Elgg install from the
 * outside, nobody can see the wiki from outside either.  This would be
 * common in corporate uses.  This is the default setting here in
 * constants.php  
 *
 * 2. Your Elgg site is open - in so much as people can see some of the
 * content, but need additional permissions to post things, etc.  In this
 * scenario, you probably have a wiki that works in a similar fashion.
 * You need to change this line in the constants.php
 * define('ELGG_MW_PUBLIC_ACTIONS', '');
 *
 * LASTLY, you probably need to do some UI stuff, such as getting rid of
 * the login/logout links in MW so you don't confuse the user.
 *
 * Also, if you want to include the tool bar, you need to handle that
 * by inserting the right line in the right place, (that is actually one
 * line of Javascript, and playing with the not-that-simple MW CSS to put
 * it up top.  (See README.txt)
 *
 */

require_once(dirname(__FILE__) . '/../../includes/Setup.php');
require_once(dirname(__FILE__) . '/../../includes/AuthPlugin.php');
require_once(dirname(__FILE__) . '/constants.php');


// fake ElggUser so that the unserialize works
class ElggUser
{
}


/*
 * Instantiate a subclass of AuthPlugin and set $wgAuth to it to
 * authenticate against an Elgg database.
 *
 * AuthPlugin is really the whole game here, technically speaking.
 *
 * MW creates this object specifically so you can do this kind of thing.
 *
 */
class ElggAuthPlugin extends AuthPlugin
{
	/*
	 *  So the constructor creates a few global variables, records what
	 *  the allowed public actions are, then creates a logout hook
	 *
	 *  It then tries to authenticate right away
	 *  
	 */
	function ElggAuthPlugin()
	{
		global $wgCookieExpiration, $wgHooks, $wgRequest;
		global $ELGG_MW_PUBLIC_PAGE_ACTIONS;

		$action = $wgRequest->getText('action', 'view');
		$title = $wgRequest->getText('title', 'Main_Page');
		
		/* bypass authentication if it's a site-wide public action or
		if it's a public action for the current page */
		$publicActions = explode(',', ELGG_MW_PUBLIC_ACTIONS);
		
		$pageHasPublicActions = false;
		if (array_key_exists($title, $ELGG_MW_PUBLIC_PAGE_ACTIONS))
		{
			$pageHasPublicActions = true;
			$pageActions = explode(',', $ELGG_MW_PUBLIC_PAGE_ACTIONS[$title]);
		}
		
		if (($pageHasPublicActions && !in_array($action, $pageActions)) || (!$pageHasPublicActions && !in_array($action, $publicActions)))
		{
			//$wgHooks['UserLogout'][] = array($this, 'logout');
			$wgCookieExpiration = ELGG_MW_ELGG_WIKI_COOKIE_EXPIRY;

			// adjust cookie expiry to 1 hr
			$this->elggAuthCookieName = ELGG_MW_ELGG_AUTH_COOKIE_NAME;
			$this->elggAuthenticate();
		}
	}


	/**
	*
	* FUNCTION FROM AuthPlugin.php
	*
	* Check whether there exists a user account with the given name.
	* The name will be normalized to MediaWiki's requirements, so
	* you might need to munge it (for instance, for lowercase initial
	* letters).
	*
	* (We are just handing off this responsibility to our own private
	* function)
	*
	* @param $username String: username.
	* @return bool
	* @public
	*/
	function userExists($username)
	{
		return $this->elggUserExists($username);
	}


	/**
	*
	* FUNCTION FROM AuthPlugin.php
	* 
	* Check if a username+password pair is a valid login.
	* The name will be normalized to MediaWiki's requirements, so
	* you might need to munge it (for instance, for lowercase initial
	* letters).
	*
	* (So this is the function where we want to trigger our own
	* authentication...)
	*
	* @param $username String: username.
	* @param $password String: user password.
	* @return bool
	* @public
	*/
	function authenticate($username, $password)
	{
		return $this->elggAuthenticate();
	}


	/**
	 *
	 * FUNCTION FROM AuthPlugin.php
	 * When creating a user account, optionally fill in preferences and such.
	 * For instance, you might pull the email address or real name from the
	 * external user database.
	 *
	 * (We're just interested in setting name, username, and email
	 * for the MediaWiki user object)
	 * 
	 * @param $user User object.
	 * @param $autocreate bool True if user is being autocreated on login
	 * @public
	 */
	function initUser(&$user)
	{
		// use cookie info to find the Elgg user
		if (!$elggUser = $this->elggGetUser())
		{
			$this->elggHandleError(ELGG_MW_ERR_USER_NOT_FOUND, "Your login unexpectedly not there.");
		}
		
		$user->mEmailAuthenticated = wfTimestampNow();

		$user->setName($elggUser->username);
		$user->setRealName($elggUser->name);
		$user->setEmail($elggUser->email);
		
		$user->setToken();
		$user->saveSettings();

		return true;
	}


	/**
	 * NEW FUNCTION
	 *
	 * This is where we kick off the real authentication
	 *
	 *
	 * @return bool - did we succeed at authentication?
	 */
	private function elggAuthenticate()
	{
		if (!$this->elggCookieThere())
		{
			// No ticket, no shirt
			//$this->elggHandleError(ELGG_MW_ERR_AUTH_FAILURE,
			//"You cannot go here directly");
			return false;
		}
		else
		{
			// We need to get the cookie value
			$cookieValue = $this->elggGetCookie();

			// So we need to let the rest of the world know what's up
			global $wgSessionStarted, $wgUser, $wgCookiePrefix, $wgCacheEpoch;

			// If there isn't a session already started properly,
			// then we have to handle the login. Otherwise, as
			// you can see at the bottom, return 'true'
			// (that they've been authenticated already)
			if (!$wgSessionStarted || $wgUser->getName() == NULL ||
				!isset($_COOKIE["{$wgCookiePrefix}UserName"]) ||
				!isset($_COOKIE["{$wgCookiePrefix}UserID"]))
			{
				// invalidate the page and topbar cache
				$wgCacheEpoch = gmdate('YmdHis', time());

				if (isset($_SESSION) && isset($_SESSION['elgg_topbar']))
				{
					unset($_SESSION['elgg_topbar']);
				}
				
				$user = $this->elggGetUser($cookieValue);
				
				if ($user == NULL)
				{
					// if unknown user, fail
					//$this->elggHandleError(ELGG_MW_ERR_USER_NOT_FOUND,
					//                       "That account was not found");
					//exit;
					// with Elgg 1.5, this cookie could be for an expired session
					// so just log the person out and return
					$this->logout($wgUser);
					return false;
				}
				else
				{
					if ($user->banned)
					{
						// if user is banned from Elgg, fail
						$this->elggHandleError(ELGG_MW_ERR_USER_BANNED, "That account is not enabled.");
						exit;
					}
					
					// This is a global function in MW
					// This was once done with User::SetupSession,
					// but got deprecated
					wfSetupSession();
					$wgSessionStarted = true;
					
					$u = User::newFromName($user->username);

					if ($u->getId() == 0)
					{
						// if account not exists in mediawiki, create it
						$u->setRealName($user->name);
						$u->addToDatabase();
						global $wgUser;
						$wgUser = $u;
					}

					$u->setCookies();
				}
			}

			return true;
		}

		return false;
	}
	

	/**
	*  NEW FUNCTION
	*
	*  Get all of the elgg user stuff from elgg
	*
	*  @param string $cookieValue
	*  @return array $elggUser - all the user info
	*  
	*/
	function elggGetUser($cookieValue)
	{
		$elggUser = NULL;
		/*$users = ELGG_DB_PREFIX . 'users';
		$user_flags = ELGG_DB_PREFIX . 'user_flags';
		$rs = $this->elggDbQuery("select $users.ident id, $users.username username, $users.name name, " .
		                         "$users.email email, $users.active active, $user_flags.value banned " .
		                         "from $users left join $user_flags " .
		                         "on $users.ident = $user_flags.user_id " .
		                         "and $user_flags.flag = 'banned' " .
		                         "where $users.user_type = 'person'" .
		                         "and $users.code = '$md5cookie';");*/
		
		$entities = ELGG_DB_PREFIX . 'entities';
		$users    = ELGG_DB_PREFIX . 'users_entity';
		$sessions = ELGG_DB_PREFIX . 'users_sessions';
		
		//error_log( 'cookie value: '.$cookieValue."\n\n" );
		// step one: get the session
		$rs = $this->elggDbQuery("SELECT data FROM  $sessions " . "WHERE session = '$cookieValue' ORDER BY ts DESC;");

		if (count($rs) > 0)
		{
			$row = $rs[0];
			$session_data = @$this->unserializesession($row['data']);
			
			// step two: get the user data
			if (isset($session_data['guid']))
			{
				$guid = $session_data['guid'];
				
				//error_log( 'session_data: '.print_r($session_data,true)."\n\n" );
				$rs = $this->elggDbQuery("SELECT $users.guid id, $users.username username, $users.name name, " . "$users.email email, $users.banned banned, $entities.enabled " . "FROM $users JOIN $entities ON ($users.guid = $entities.guid) " . "WHERE $users.guid = '$guid';");
				
				if (count($rs) > 0)
				{
					$row                = $rs[0];
					$elggUser->username = $row['username'];
					$elggUser->name     = $row['name'];
					$elggUser->email    = $row['email'];
					$elggUser->active   = $row['enabled'] == 'yes';
					$elggUser->banned   = $row['banned'] == 1;
				}

				//error_log(print_r($elggUser,true));
				return $elggUser;
			}
		}
	}
	

	/**
	 *  NEW FUNCTION
	 *
	 *  Do we have this user as an elgg user?
	 *
	 *  @param string $username
	 *  @return bool - Does the username exist?
	 *  
	 */
	private function elggUserExists($username)
	{
		$rs = $this->elggDbQuery("select count(*) from " . ELGG_DB_PREFIX . "users_entity where username = '$username';");
		return count($rs) > 0;
	}


	/**
	 *  NEW FUNCTION
	 *
	 *  Unserialise session data
	 *
	 *  @param string $data
	 *  @return array - the session data
	 *  
	 */
	private function unserializesession($data)
	{
		$vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/', $data, - 1, PREG_SPLIT_NO_EMPTY| PREG_SPLIT_DELIM_CAPTURE);

		for ($i = 0; $vars[$i]; $i++)
		{
			$result[$vars[$i++]] = unserialize($vars[$i]);
		}

		return $result;
	}
	
	/**
	 *  NEW FUNCTION
	 *
	 *  This utility method actually does the lookup in the elgg
	 *  database.
	 *
	 *  You really don't want to be giving out database info on
	 *  the error page.  Make sure you set the RUN_MODE value
	 *  in constants.php to something other than "testing"
	 *
	 *  @param string $query - the query string
	 *  @return array $rows - the resulting rows
	 *  
	 */
	private function elggDbQuery($query)
	{
		if (!$cnx = mysql_connect(ELGG_DB_HOST, ELGG_DB_USER, ELGG_DB_PASSWORD))
		{
			$this->elggHandleError('Could not connect to database: ' . mysql_error(), 'Could not authenticate you. Error condition 111.');
		}
		
		if (!mysql_select_db(ELGG_DB_NAME, $cnx))
		{
			
			$this->elggHandleError('Could not select database: ' . mysql_error(), 'Could not authenticate you. Error condition 222.');
		}
		
		if (!$rs = mysql_query($query, $cnx))
		{
			$this->elggHandleError('Could not execute query: ' . mysql_error(), 'Could not authenticate you. Error condition 333.');
		}
		
		$rows = array();
		
		while ($row = mysql_fetch_assoc($rs))
		{
			$rows[] = $row;
		}

		return $rows;
	}


	/**
	 *  NEW FUNCTION
	 *
	 *  This is the logout "hook" function we created earlier
	 *  in this class
	 *  
	 */
	function logout($user = NULL)
	{
		global $wgUser;
		$u = $user == NULL ? $wgUser : $user;
		$u->logout();
	}


	/**
	 *  NEW FUNCTION
	 *
	 *  Here, we check to see if there is an elgg cookie
	 *
	 *  @private
	 *  
	 */
	private function elggCookieThere()
	{
		return(array_key_exists($this->elggAuthCookieName, $_COOKIE));
	}


	/**
	 *  NEW FUNCTION
	 *
	 *  Here, we grab the elgg cookie value
	 *
	 *  @private
	 *  
	 */
	private function elggGetCookie()
	{
		return $_COOKIE[$this->elggAuthCookieName];
	}
	

	/**
	 *  NEW FUNCTION
	 *
	 *  Produce an error message on a new page if something goes
	 *  wrong (error.php)
	 *  
	 *  
	 */
	private function elggHandleError($detailmessage, $productionmessage)
	{
		global $wgScriptPath, $wgUser;
		
		if (RUN_MODE == "testing")
		{
			$message = $detailmessage;
		}
		else
		{
			$message = $productionmessage;
		}
		
		// We don't really want to logout
		// perhaps someone else can make this next line work
		//$this->logout($wgUser);
		//error_log($detailmessage.':'.$productionmessage);
		header("Location: $wgScriptPath/extensions/elgg/error.php?message=" . urlencode($message));
		exit;
	}


	/**
	 * FUNCTION FROM AuthPlugin.php
	 *
	 * When a user logs in, optionally fill in preferences and such.
	 * For instance, you might pull the email address or real name from the
	 * external user database.
	 *
	 * The User object is passed by reference so it can be modified; don't
	 * forget the & on your function declaration.
	 *
	 * (In the original function, it just returns true)
	 *
	 * @param User $user
	 * @public
	 */
	function updateUser(&$user)
	{
		/*
		 * Steps:
		 * 1.
		 *
		 *
		 */
		
		if (!$cookieValue == $this->elggGetCookie())
		{
			// if missing Elgg cookie, fail
			$this->elggHandleError(ELGG_MW_ERR_AUTH_FAILURE, "There was a problem with your identity.");
		}
		
		if (!$elggUser == $this->elggGetUser($cookieValue))
		{
			$this->elggHandleError(ELGG_MW_ERR_USER_NOT_FOUND, "There was a problem figuring out who you are.");
		}
		
		$user->setName($elggUser->username);
		$user->getRealName($elggUser->name);
		$user->getEmail($elggUser->email);
		
		return true;
	}


	/**
	 *
	 * FUNCTION FROM AuthPlugin.php
	 * 
	 * Modify options in the login template.
	 *
	 * @param $template UserLoginTemplate object.
	 * @public
	 */
	function modifyUITemplate(&$template)
	{
		$template->set('useemail', false);
		$template->set('remember', false);
		$template->set('create', false);
		$template->set('domain', false);
		$template->set('usedomain', false);
	}


	/**
	 * 
	 * FUNCTION FROM AuthPlugin.php
	 *
	 * Here we're going to check to see that the domain
	 * is the one we want.
	 * 
	 * @param $domain String: authentication domain.
	 * @return bool
	 * @public
	 */
	function validDomain($domain)
	{
		if (CHECK_DOMAIN)
		{
			return VALID_DOMAIN == $domain;
		}
		else
		{
			return true;
		}
	}


	/**
	 *
	 * FUNCTION FROM AuthPlugin.php
	 * 
	 * Set the domain this plugin is supposed to use when authenticating.
	 *
	 * @param $domain String: authentication domain.
	 * @public
	 */
	function setDomain($domain)
	{
		$this->domain = $domain;
	}
	

	/**
	 * 
	 * FUNCTION FROM AuthPlugin.php
	 * 
	 * Set the given password in the authentication database.
	 * As a special case, the password may be set to null to request
	 * locking the password to an unusable value, with the expectation
	 * that it will be set later through a mail reset or other method.
	 *
	 * Return true if successful.
	 * (We will be returning false)
	 *
	 * @param $user User object.
	 * @param $password String: password.
	 * @return bool
	 * @public
	 */
	function setPassword($user, $password)
	{
		return false;
	}


	/**
	 * FUNCTION FROM AuthPlugin.php
	 *
	 * Add a user to the external authentication database.
	 * Return true if successful.
	 *
	 * (We are going to be returning false here)
	 *
	 * @param User $user - only the name should be assumed valid at this point
	 * @param string $password
	 * @param string $email
	 * @param string $realname
	 * @return bool
	 * @public
	 */
	function addUser($user, $password, $email = '', $realname = '')
	{
		return true;
	}


	/*
	 * FUNCTION FROM AuthPlugin.php
	 *
	 * From superclass definition:
	 * 
	 * If you want to munge the case of an account name before the final
	 * check, now is your chance.
	 *
	 * (We're not doing that)
	 */
	function getCanonicalName($username)
	{
		$username = strtolower($username);
		$username[0] = strtoupper($username[0]);
		return $username;
	}



	/*
	 *  FUNCTION FROM AuthPlugin.php
	 *  
	 * Update user information in the external authentication database.
	 * Return true if successful.
	 *
	 * We don't want to update the Elgg user database from here
	 *
	 * @param $user User object.
	 *
	 *  @return bool
	 *  @public
	 * 
	 */
	function updateExternalDB($user)
	{
		return false;
	}


	/*
	 *  FUNCTION FROM AuthPlugin.php
	 *  
	 *  Check to see if external accounts can be created. We don't want
	 *  this to happen
	 *
	 *  @return bool
	 *  @public
	 * 
	 */
	function canCreateAccounts()
	{
		return false;
	}


	/*
	 *  FUNCTION FROM AuthPlugin.php
	 *  
	 *  This function determines whether or not we should create a user
	 *  account if one does not exist in MediaWiki.  It should return
	 *  true or false, and not do anything else.
	 *
	 *  @return bool
	 *  @public
	 * 
	 */
	function autoCreate()
	{
		return true;
	}


	/**
	 * This function, according to the superclass def,
	 * should return true to prevent logins that don't authenticate here f
	 * from being checked against the local database's password fields.
	 *
	 *  @return bool
	 *  @public
	 *
	 */
	function strict()
	{
		return true;
	}
}

?>
