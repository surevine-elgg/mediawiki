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


$body = '';

$mediawiki_url = get_plugin_setting('url', 'mediawiki');

$body .= elgg_echo('mediawiki:url:settings:title');
$body .= '<br />';
$body .= elgg_view('input/text', array('internalname' => 'params[url]',
										'value' => $mediawiki_url));

$body .= '<br /><br />';

$mediawiki_proxyurl = get_plugin_setting('proxyurl', 'mediawiki');

$body .= elgg_echo('mediawiki:proxyurl:settings:title');
$body .= '<br />';
$body .= elgg_view('input/text', array('internalname' => 'params[proxyurl]',
										'value' => $mediawiki_proxyurl));

$body .= '<br /><br />';

$mediawiki_digest_interval = get_plugin_setting('digest_interval', 'mediawiki');

$body .= elgg_echo('mediawiki:digest_interval:settings:title');
$body .= '<br />';
$body .= elgg_view('input/text', array('internalname' => 'params[digest_interval]',
										'value' => $mediawiki_digest_interval));

$body .= '<br /><br />';

$options = array(elgg_echo('mediawiki:settings:group_profile_display_option:left') => 'left',
		elgg_echo('mediawiki:settings:group_profile_display_option:right') => 'right',
		elgg_echo('mediawiki:settings:group_profile_display_option:none') => 'none',);

$mediawiki_group_profile_display = get_plugin_setting('group_profile_display', 'mediawiki');

if (!$mediawiki_group_profile_display)
{
	$mediawiki_group_profile_display = 'left';
}

$body .= elgg_echo('mediawiki:settings:group_profile_display:title') . '<br />';
$body .= elgg_view('input/radio', array('internalname' => 'params[group_profile_display]',
					'value' => $mediawiki_group_profile_display,
					'options' => $options));

echo $body;

