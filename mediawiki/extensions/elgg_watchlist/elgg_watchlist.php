<?php

/**
	* Elgg Mediawiki integration plugin
	*
	* An extension that adds the Elgg watchlist tab to each editable page
	*
	* @package Mediawiki
	* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	* @author Kevin Jardine <kevin.jardine@surevine.com>
	* @copyright Surevine Limited 2009
	* @link http://www.surevine.com/
*/


$wgExtensionFunctions[] = 'wfElggWatchlist';
$wgExtensionCredits['other'][] = array('name' => 'Elgg watchlist', 'author' => 'Kevin Jardine', 'description' => 'Adds the Elgg watchlist tab to each editable page.',);


function wfElggWatchlist()
{
	global $wgHooks, $wgMessageCache, $wgUser;

	if ($wgUser->isLoggedIn())
	{
		// only logged-in users get to see this
		$wgMessageCache->addMessage('myact', 'Elgg watch list');
		$wgHooks['SkinTemplateContentActions'][] = 'wfElggWatchlistContentHook';
		$wgHooks['UnknownAction'][] = 'wfElggWatchlistDisplayForm';
	}
}


function wfElggWatchlistContentHook(&$content_actions)
{
	global $wgRequest, $wgRequest, $wgTitle;
	
	$action = $wgRequest->getText('action');
	
	if ($wgTitle->getNamespace() != NS_SPECIAL)
	{
		$content_actions['myact'] = array('class' => $action == 'myact' ? 'selected' : false,
						'text' => wfMsg('myact'),
						'href' => $wgTitle->getLocalUrl('action=myact'));
	}
	
	return true;
}


function wfElggWatchlistDisplayForm($action, &$wgArticle)
{
	global $wgOut;
	
	if ($action == 'myact')
	{
		$title = $wgArticle->getTitle();
		//$page = $title->getIndexTitle();
		$page = wfUrlencode($title->getPrefixedDBkey());
		$ts = time();
		$elgg_url = ELGG_URL;
		
		$html = <<<END
			<script type="text/javascript">

			$(document).ready(function()
				{
					$('#elggwiki_watch_form').load("{$elgg_url}mod/mediawiki/get_watch_form_groups.php?page=$page&ts=$ts");
				}
			);

			</script>

			<div id="elggwiki_watch_form"></div>
END;
		//$wgOut->addHTML( 'The page name is ' . $title->getText() . ' and you are ' . $wgArticle->getUserText() );
		$wgOut->addHTML($html);
	}

	return false;
}
