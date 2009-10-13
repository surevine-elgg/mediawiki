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


if (!$vars['entity']->num_display)
{
	$num_display = 5;
}
else
{
	$num_display = $vars['entity']->num_display;
}
?>

<p>

	<?php echo elgg_echo("mediawiki:num_display"); ?>:
	<select name="params[num_display]">
		<option value="1" <?php if ($num_display == 1) echo "SELECTED"; ?>>1</option>
		<option value="2" <?php if ($num_display == 2) echo "SELECTED"; ?>>2</option>
		<option value="3" <?php if ($num_display == 3) echo "SELECTED"; ?>>3</option>
		<option value="4" <?php if ($num_display == 4) echo "SELECTED"; ?>>4</option>
		<option value="5" <?php if ($num_display == 5) echo "SELECTED"; ?>>5</option>
		<option value="6" <?php if ($num_display == 6) echo "SELECTED"; ?>>6</option>
		<option value="7" <?php if ($num_display == 7) echo "SELECTED"; ?>>7</option>
		<option value="8" <?php if ($num_display == 8) echo "SELECTED"; ?>>8</option>
		<option value="9" <?php if ($num_display == 9) echo "SELECTED"; ?>>9</option>
		<option value="10" <?php if ($num_display == 10) echo "SELECTED"; ?>>10</option>
	</select>
</p>

<?php

$value = $vars['entity']->minor_edits;

if (!$value)
{
	$value = 'no';
}

echo '<label>' . elgg_echo('mediawiki:widget:setting:minor_edits:description') . '<br />';
$options = array(elgg_echo('mediawiki:widget:setting:yes') => 'yes', elgg_echo('mediawiki:widget:setting:no') => 'no');
echo elgg_view('input/radio', array('internalname' => 'params[minor_edits]', 'value' => $value, 'options' => $options));
echo '</label>';

?>
