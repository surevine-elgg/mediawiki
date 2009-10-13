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


$values    = array();
$options   = array();
$user_guid = get_loggedin_userid();

if ($user_guid)
{
	$groups = get_entities('group', '', $user_guid, '', 500);

	if ($groups)
	{
		$watchlist = mediawiki_get_watchlist($vars['page']);
		
		foreach ($groups as $group)
		{
			$group_guid = $group->getGUID();
			$options[$group->name] = $group_guid;

			if (in_array($group_guid, $watchlist))
			{
				$values[] = $group_guid;
			}
		}
	}
	
	$page = $vars['page'];
	$mediawiki_proxyurl = get_plugin_setting('proxyurl', 'mediawiki');

	if ($mediawiki_proxyurl)
	{
		$action = $mediawiki_proxyurl . 'action/mediawiki/watch';
	}
	else
	{
		$action = $vars['config']->url . 'action/mediawiki/watch';
	}
	
	$submit_message = elgg_echo('mediawiki:watch_form:submit_message');

	$js = <<<END
	<script type="text/javascript">
	$(document).ready(function() {
	$("input[name=submit_button]").click(function() {
			var page = $("input[name=page]").val();
			
			var qs = "page="+page;
			
			$("input[class=mediawiki_personal_watchlist]:checked").each( function() {
				qs += "&personal_watchlist[]="+$(this).val();
			});
			$("input[class=mediawiki_group_watchlist]:checked").each( function() {
				qs += "&group_watchlist[]="+$(this).val();
			});
			
			$('#elggwiki_watch_form').html("<p>$submit_message</p>");
			
			$.ajax({
	      type: "POST",
	      url: "$action",
	      data: qs,
	      success: function(response,code) {
	        $('#elggwiki_watch_form').html(response);
	      }
	     });
	    return false;
		});
	});
	</script>
END;
	
	$personal_options = array(elgg_echo('mediawiki:personal_options:description') => 'yes');
	$personal_value = mediawiki_get_personal_watchlist($page, $user_guid);
	
	echo $js;
	echo "<form action=\"\" method=\"post\">";
	echo elgg_view('input/checkboxes', array('class' => 'mediawiki_personal_watchlist',
						'internalname' => 'personal_watchlist',
						'options' => $personal_options,
						'value' => $personal_value));
	if ($options)
	{
		echo '<p>' . elgg_echo('mediawiki:watch_form:description') . '</p>';
		echo elgg_view('input/checkboxes', array('class' => 'mediawiki_group_watchlist',
							'internalname' => 'group_watchlist',
							'options' => $options,
							'value' => $values));
	}

	echo elgg_view('input/hidden', array('internalname' => 'page', 'value' => $page));
	echo '<br />';

	echo elgg_view('input/submit', array('internalname' => 'submit_button', 'value' => elgg_echo('save')));
	echo "</form>";
}
?>
