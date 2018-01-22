/**
 * 2013-2014 Froggy Commerce
 *
 * NOTICE OF LICENSE
 *
 * You should have received a licence with this module.
 * If you didn't buy this module on Froggy-Commerce.com, ThemeForest.net
 * or Addons.PrestaShop.com, please contact us immediately : contact@froggy-commerce.com
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to benefit the updates
 * for newer PrestaShop versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author Froggy Commerce <contact@froggy-commerce.com>
 *  @copyright  2013-2014 Froggy Commerce
 */

function loadFroggyHistoryLog(link)
{
	var ajax_url = '../modules/froggyhistory/ajax.php' + link;

	// Compliancy with PS 1.7
	if (window.location.href.indexOf('product/form') >= 0) {
        ajax_url = fcdirname(fcdirname(fcdirname(fcdirname(window.location.href)))) + '/' + ajax_url;
	}

	$.ajax({
		url: ajax_url,
		success: function(data) {
			$('#froggyhistory-list').html(data);
		}
	});
}

function initFroggyHistoryPagination()
{
	$(".froggyhistory-pagination").click(function() {
		$('#froggyhistory-list').append('<p align="center"><img src="../modules/froggyhistory/views/img/loader.gif" /></p>');
		loadFroggyHistoryLog($(this).attr('href'));
		return false;
	});
}

function fcdirname(path)
{
    return path.replace(/\\/g, '/').replace(/\/[^/]*\/?$/, '');
}

$(document).ready(function() {
	if ($("#froggyhistory-introduction-configuration a").length)
    	$("#froggyhistory-introduction-configuration a").fancybox();
});