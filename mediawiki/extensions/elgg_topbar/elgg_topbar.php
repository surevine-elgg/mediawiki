<?php

/**
	* Elgg Mediawiki integration plugin
	*
	* An extension that adds the Elgg topbar
	* Requires the Elgg authentication extension
	*
	* @package Mediawiki
	* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	* @author Kevin Jardine <kevin.jardine@surevine.com>
	* @copyright Surevine Limited 2009
	* @link http://www.surevine.com/
*/

$wgExtensionFunctions[] = 'wfElggTopbar';
$wgExtensionCredits['other'][] = array('name' => 'Elgg topbar', 'author' => 'Kevin Jardine', 'description' => 'Adds the Elgg topbar to each page.',);


function wfElggTopbar()
{
	global $wgHooks, $wgUser, $wgCacheEpoch;
	$wgHooks['SkinAfterBottomScripts'][] = 'wfElggTopbarAddJS';

	if ((!isset($_SESSION['elgg_topbar']) || !$_SESSION['elgg_topbar']) && ($wgUser->isLoggedIn()))
	{
		// invalidate the page cache
		$wgCacheEpoch = gmdate('YmdHis', time());
	}
}


function wfElggTopbarAddJS($skin, &$text)
{
	global $wgScriptPath, $wgCacheEpoch, $wgUser;
	
	$elgg_url = ELGG_URL;
	
	// cache topbar
	if (isset($_SESSION['elgg_topbar']))
	{
		$elgg_topbar = $_SESSION['elgg_topbar'];
	}
	else
	{
		$elgg_topbar = '';
	}
	
	if (!$elgg_topbar && ($wgUser->isLoggedIn()))
	{
		
		$text .= <<<END
			<script type="text/javascript">

			function save_topbar(topbar)
			{
				$.post("$wgScriptPath/set_topbar.php", {'elgg_topbar':topbar});
				$('#elggwiki_topbar').html(topbar);
				$('ul.topbardropdownmenu').elgg_topbardropdownmenu();
			}

			$.get("{$elgg_url}mod/mediawiki/mediawiki_topbar.php", save_topbar);

			</script>
END;
	}
	
	return true;
}
