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
	$.ajax({
		url: '../modules/froggyhistory/ajax.php' + link,
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


$(document).ready(function() {
    $('#froggyhistory-see-general-history').click(function() {
        $('#froggyhistory-introduction').hide();
        $('#froggyhistory-configuration').hide();
        $('#froggyhistory-general').fadeIn();
    });
});