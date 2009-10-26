<?php

/**
	* Elgg Mediawiki integration plugin
	*
	* An extension that notifies the Elgg Mediawiki plugin when a page is updated
	* The Elgg authentication extension is recommended but not required
	*
	* @package Mediawiki
	* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	* @author Kevin Jardine <kevin.jardine@surevine.com>
	* @copyright Surevine Limited 2009
	* @link http://www.surevine.com/
*/


$wgExtensionFunctions[] = 'wfElggNotify';
$wgExtensionCredits['other'][] = array('name' => 'Elgg notify', 'author' => 'Kevin Jardine', 'description' => 'Notifies the Elgg Mediawiki plugin when a page is updated.',);

/**
 * wfElggNotify 
 * 
 * @return void
 */
function wfElggNotify()
{
	global $wgHooks;
	$wgHooks['ArticleSaveComplete'][] = 'wfElggNotifyCurlUpdate';
}

/**
 * wfElggNotifyCurlUpdate 
 * 
 * @param object $article 
 * @param object $user 
 * @param bool $isMinor 
 * @param bool $isWatch 
 * @return bool
 */
function wfElggNotifyCurlUpdate($article, $user, $text, $summary, $isMinor, $isWatch, $section, $flags, $revision)
{
	if ($isMinor)
	{
		$editStatus = 'minor';
	}
	else
	{
		$editStatus = 'major';
	}
	
	$title = $article->getTitle();
	$page = wfUrlencode($title->getPrefixedDBkey());
	$username = $user->mName;
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, ELGG_URL . "mod/mediawiki/edit_notify.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "page=$page&mediawiki_username=$username&edit_status=$editStatus");
	
	curl_exec($ch);
	curl_close($ch);
	
	return true;
}
