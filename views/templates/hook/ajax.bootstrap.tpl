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
    <li>
        <p><strong>{$log.sentence.hour} :</strong> {$log.sentence.description}{if isset($log.sentence.diff)}<a href="#{$log.id_fhy_log}" class="froggy-history-details">{l s='See details' mod='froggyhistory'}</a>{/if}</p>

        {if isset($log.sentence.diff)}
            <div id="froggy-history-details-div-{$log.id_fhy_log|intval}" class="froggy-history-details-div">
                {foreach from=$log.sentence.diff key=field item=values}
                    {if isset($values.before)}
                        <dl>
                            <dt><strong>- {l s='Field' mod='froggyhistory'} "{$field|escape:'html':'UTF-8'}":</strong></dt>
                            <dd><u>{l s='Before modification:' mod='froggyhistory'}</u> {$values.before}</dd>
                            <dd><u>{l s='After modification:' mod='froggyhistory'}</u> {$values.after}</dd>
                        </dl>
                        {else}
                        {foreach from=$values key=iso_lang item=values_lang}
                            <strong>- {l s='Field' mod='froggyhistory'} "{$field|escape:'html':'UTF-8'}" {l s='in' mod='froggyhistory'} "{$iso_lang|escape:'html':'UTF-8'}":</strong><br>
                            <u>{l s='Before modification:' mod='froggyhistory'}</u> {$values_lang.before}<br>
                            <u>{l s='After modification:' mod='froggyhistory'}</u> {$values_lang.after}<br>
                        {/foreach}
                    {/if}
                {/foreach}
            </div>
        {/if}
    </li>
    {/foreach}
    {if $froggyhistory.nb_pages gt 2}
    <br />
        {section name=pagination start=1 loop=$froggyhistory.nb_pages step=1}
            {if $smarty.section.pagination.index eq $froggyhistory.page}<b>{else}<a href="{$froggyhistory.url|escape:'html':'UTF-8'}&page={$smarty.section.pagination.index|intval}" class="froggyhistory-pagination">{/if}{$smarty.section.pagination.index}{if $smarty.section.pagination.index eq $froggyhistory.page}</b>{else}</a>{/if}
        {/section}
    <script>$(document).ready(function() { initFroggyHistoryPagination(); });</script>
    {/if}
    {else}
    {l s='No log registered yet' mod='froggyhistory'}
{/if}

{literal}
<script>
    $('.froggy-history-details').unbind('click').bind('click', function() {
        var froggy_history_id = $(this).attr('href').replace('#', '');
        if ($('#froggy-history-details-div-'+froggy_history_id).is(':visible'))
        {
            $('#froggy-history-details-div-'+froggy_history_id).slideUp();
            $(this).parent().parent().removeClass('active');
        }
        else
        {
            $('#froggy-history-details-div-'+froggy_history_id).slideDown();
            $(this).parent().parent().addClass('active');
        }
        return false;
    });
</script>
{/literal}