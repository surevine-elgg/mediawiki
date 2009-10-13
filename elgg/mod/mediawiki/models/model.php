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


if (false === function_exists('lcfirst'))
{
	/**
	 * Make a string's first character lowercase
	 *
	 * @param string $str
	 * @return string the resulting string.
	 */

	function lcfirst($str)
	{
		$str[0] = strtolower($str[0]);
		return (string) $str;
	}
}


function mediawiki_vsort($original, $field, $descending = false)
{
	if (!$original)
	{
		return $original;
	}

	$sortArr = array();
	
	foreach ($original as $key => $item)
	{
		$sortArr[$key] = $item->$field;
	}
	
	if ($descending)
	{
		arsort($sortArr);
	}
	else
	{
		asort($sortArr);
	}
	
	$resultArr = array();

	foreach ($sortArr as $key => $value)
	{
		$resultArr[$key] = $original[$key];
	}
	
	return $resultArr;
}


/**
 * Annotate watch object if it exists
 * 
 * @param string $page Mediwiki page that has been updated
 * @param string $mediwiki_username Username from Mediawiki 
 * @param string $edit_status - Mediawiki edit status (major/minor)
 */
function mediawiki_log_page_edit($page, $mediawiki_username, $edit_status)
{
	error_log("\n\nmediawiki_log_page_edit($page,$mediawiki_username,$edit_status)\n\n");

	$user = mediawiki_get_user_by_username($mediawiki_username);

	if ($user)
	{
		// is there a watch on this page?
		$watch = get_entities_from_metadata('page', $page, "object", "mediawiki_watch");
		
		if ($watch)
		{
			// if so, add the edit details to the watch object as an annotation
			$watch = $watch[0];
			$watch->annotate('edit', $edit_status, ACCESS_PUBLIC, $user->getGUID(), 'text');
			//error_log("\n\nadded edit log\n\n");
		}
	}
}


/**
 * Get an array of the most recent edits watched by a group or user
 * 
 * @param int $entity group or user doing the watching
 * @param int $num The number of results to return (code supports at most 50)
 * @param boolean $minor If false returns only major edits
 * 
 * @return array An array of objects describing edits
 */
function mediawiki_get_watched_edits($entity, $num, $minor = true)
{
	$max_num = 50;
	$edits = array();

	if ($entity instanceOf ElggGroup)
	{
		$group_guid = $entity->getGUID();

		// work around for core Elgg bug #934
		// see: http://trac.elgg.org/elgg/ticket/934

		if (!get_metastring_id($group_guid))
		{
			return $edits;
		}
		// end of workaround

		$watches = get_entities_from_annotations("object", "mediawiki_watch", "group", $group_guid);
	}
	else
	{
		// should be a personal watch then
		$watches = get_entities_from_annotations("object", "mediawiki_watch", "personal", "yes", $entity->getGUID());
	}

	if ($watches)
	{
		$mediawiki_digest_interval = get_plugin_setting('digest_interval', 'mediawiki');

		if ($mediawiki_digest_interval)
		{
			$digest_interval = $mediawiki_digest_interval * 60;
		}
		else
		{
			$digest_interval = 0;
		}

		foreach ($watches as $watch)
		{
			if (!$minor)
			{
				$watch_edits = get_annotations($watch->getGUID(), "object", "mediawiki_watch", "edit", "major", 0, $max_num);
			}
			else
			{
				$watch_edits = $watch->getAnnotations('edit', $max_num);
			}

			if ($watch_edits)
			{
				$previous_edit = new stdClass();

				foreach ($watch_edits as $watch_edit)
				{
					$edit              = new stdClass();
					$edit->page        = $watch->page;
					$edit->update_time = $watch_edit->time_created;
					$edit->user_guid   = $watch_edit->owner_guid;
					$edit->status      = $watch_edit->value;

					// collapse multiple consecutive edits by the same person if within the digest interval
					if ($digest_interval && isset($previous_edit->update_time) &&
						($previous_edit->user_guid == $edit->user_guid) &&
						(($edit->update_time - $previous_edit->update_time) <= $digest_interval))
					{
						
						array_pop($edits);
					}

					$edits[] = $edit;
					$previous_edit = $edit;
				}
			}
		}
	}

	return array_slice(mediawiki_vsort($edits, 'update_time', true), 0, $num);
}


/**
 * Try to find an Elgg user that corresponds to the given Mediawiki username
 * Mediawiki insists that usernames start with a capital letter, 
 * so search Elgg for usernames that start with a capital or lowercase
 * 
 * @param string $mediawiki_username
 * 
 * @return ElggUser|boolean An ElggUser or false if a corresponding user could not be found
 */
function mediawiki_get_user_by_username($mediwiki_username)
{
	$user = get_user_by_username($mediwiki_username);

	if ($user)
	{
		return $user;
	}
	else
	{
		// try lowercasing the first letter
		$user = get_user_by_username(lcfirst($mediwiki_username));

		if ($user)
		{
			return $user;
		}
	}

	// could not find the user, so report failure
	return false;
}


/**
 * Get a list of all the guids for the groups watching this page
 * 
 * @param string $page The Mediawiki page being watched
 * 
 * @return array Group guids
 */
function mediawiki_get_watchlist($page)
{
	$watchlist = array();
	$watch = get_entities_from_metadata('page', $page, "object", "mediawiki_watch");

	if ($watch)
	{
		$watch = $watch[0];
		$watched_groups = $watch->getAnnotations('group', 500);

		if ($watched_groups)
		{
			foreach ($watched_groups as $watched_group)
			{
				$watchlist[] = $watched_group->value;
			}
		}
	}
	return $watchlist;
}


function mediawiki_get_personal_watchlist($page, $user_guid)
{
	$watchlist = array();
	$watch = get_entities_from_metadata('page', $page, "object", "mediawiki_watch");

	if ($watch)
	{
		$watch = $watch[0];
		$watch_option = get_annotations($watch->getGUID(), "object", "mediawiki_watch", "personal", "yes", $user_guid);

		if ($watch_option)
		{
			return 'yes';
		}
	}

	return 'no';
}


/**
 * Clear all the group watches held by this user
 * 
 * @param ElggObject $watch The watch entity
 * @param int $user_guid The guid for the user
 * 
 */
function mediawiki_clear_group_watchlist($watch, $user_guid)
{
	$watched_groups = get_annotations($watch->getGUID(), "object", "mediawiki_watch", "group", "", $user_guid);

	if ($watched_groups)
	{
		foreach ($watched_groups as $watched_group)
		{
			$watched_group->delete();
		}
	}
}


/**
 * Clear a personal watch annotation
 * 
 * @param ElggObject $watch The watch entity
 * @param int $user_guid The guid for the user
 * 
 */
function mediawiki_clear_personal_watchlist($watch, $user_guid)
{
	$watched = get_annotations($watch->getGUID(), "object", "mediawiki_watch", "personal", "", $user_guid);

	if ($watched)
	{
		foreach ($watched as $watch_item)
		{
			$watch_item->delete();
		}
	}
}


/**
 * Add a watch for a group from a user
 * 
 * @param ElggObject $watch The watch entity
 * @param int $group_guid The guid for the group doing the watching
 * @param int $user_guid Should be the group owner
 * 
 */
function mediawiki_set_group_watch($watch, $group_guid, $user_guid)
{
	$watch->annotate('group', $group_guid, ACCESS_PUBLIC, $user_guid);
}


/**
 * Add a personal watch for a user
 * 
 * @param ElggObject $watch The watch entity
 * @param int $user_guid Should be the group owner
 * 
 */
function mediawiki_set_personal_watch($watch, $user_guid)
{
	$watch->annotate('personal', 'yes', ACCESS_PUBLIC, $user_guid);
}


/**
 * Adds a group watchlist for a specific page
 * 
 * @param string $page The Mediawiki page being watched
 * @param array $watchlist An array of group guids
 * 
 */
function mediawiki_set_group_watchlist($page, $watchlist)
{
	$user_guid = get_loggedin_userid();

	if ($user_guid)
	{
		$watch = get_entities_from_metadata('page', $page, "object", "mediawiki_watch");

		if ($watch)
		{
			$watch = $watch[0];
			mediawiki_clear_group_watchlist($watch, $user_guid);
		}
		else
		{
			if ($watchlist)
			{
				$watch = mediawiki_create_watch_object($page, $user_guid);
			}
		}
		
		if ($watchlist)
		{
			foreach ($watchlist as $group_guid)
			{
				mediawiki_set_group_watch($watch, $group_guid, $user_guid);
			}
		}
		else
		{
			if ($watch)
			{
				// if there are no more watches, delete the watch entity
				// commented out for now as we may want the watch list archived data
				/*
				$watch_count = $watch->countAnnotations('group');

				if (!$watch_count)
				{
					$watch->delete();
				}
				*/
			}
		}
	}
}


/**
 * Adds a personal watchlist for a specific page
 * 
 * @param string $page The Mediawiki page being watched
 * @param array $watchlist An array of options (currently only yes)
 * 
 */
function mediawiki_set_personal_watchlist($page, $user_guid, $status)
{
	if ($user_guid)
	{
		$watch = get_entities_from_metadata('page', $page, "object", "mediawiki_watch");

		if ($watch)
		{
			$watch = $watch[0];
			mediawiki_clear_personal_watchlist($watch, $user_guid);
		}
		else
		{
			if ($status)
			{
				$watch = mediawiki_create_watch_object($page, $user_guid);
			}
		}

		if ($status)
		{
			mediawiki_set_personal_watch($watch, $user_guid);
		}
	}
}


function mediawiki_create_watch_object($page, $user_guid)
{
	$watch             = new ElggObject();
	$watch->subtype    = 'mediawiki_watch';
	$watch->owner_guid = $user_guid;
	$watch->access_id  = ACCESS_PUBLIC;
	$watch->page       = $page;
	$watch->save();
	
	return $watch;
}


?>
