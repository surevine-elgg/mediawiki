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


// Load Elgg engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
?>

<script type="text/javascript">

// ELGG TOOLBAR MENU
$.fn.elgg_topbardropdownmenu = function(options)
	{
		options = $.extend({speed: 350}, options || {});
  
		this.each(function()
		{
			var root = this, zIndex = 5000;
    
			function getSubnav(ele)
			{
				if (ele.nodeName.toLowerCase() == 'li')
				{
					var subnav = $('> ul', ele);
					return subnav.length ? subnav[0] : null;
				}
				else
				{
					return ele;
				}
			}
    
			function getActuator(ele)
			{
				if (ele.nodeName.toLowerCase() == 'ul')
				{
					return $(ele).parents('li')[0];
				}
				else
				{
					return ele;
				}
			}

			function hide()
			{
				var subnav = getSubnav(this);

				if (!subnav)
					return;

				$.data(subnav, 'cancelHide', false);

				setTimeout(function()
					{
						if (!$.data(subnav, 'cancelHide'))
						{
							$(subnav).slideUp(100);
						}
					}, 250);
			}
 
			function show()
			{
				var subnav = getSubnav(this);

				if (!subnav)
					return;

				$.data(subnav, 'cancelHide', true);
				$(subnav).css({zIndex: zIndex++}).slideDown(options.speed);

				if (this.nodeName.toLowerCase() == 'ul')
				{
					var li = getActuator(this);
					$(li).addClass('hover');
					$('> a', li).addClass('hover');
				}
			}
    
			$('ul, li', this).hover(show, hide);

			$('li', this).hover(
					function() { $(this).addClass('hover'); $('> a', this).addClass('hover'); },
					function() { $(this).removeClass('hover'); $('> a', this).removeClass('hover'); }
				);
    
		});
	};

</script>

<?php
echo elgg_view('page_elements/elgg_topbar');
