{**
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
*}

{if $froggyhistory.history_log_list|@count gt 0}
	{foreach from=$froggyhistory.history_log_list item=log}
		<strong>{$log.sentence.hour} :</strong> {$log.sentence.description}<br />
	{/foreach}
	{if $froggyhistory.nb_pages gt 2}
		<br />
		{section name=pagination start=1 loop=$froggyhistory.nb_pages step=1}
			{if $smarty.section.pagination.index eq $froggyhistory.page}<b>{else}<a href="{$froggyhistory.url}&page={$smarty.section.pagination.index}" class="froggyhistory_pagination">{/if}{$smarty.section.pagination.index}{if $smarty.section.pagination.index eq $froggyhistory.page}</b>{else}</a>{/if}
		{/section}
		<script>$(document).ready(function() { initFroggyHistoryPagination(); });</script>
	{/if}
{else}
	{l s='No log registered yet' mod='froggyhistory'}
{/if}