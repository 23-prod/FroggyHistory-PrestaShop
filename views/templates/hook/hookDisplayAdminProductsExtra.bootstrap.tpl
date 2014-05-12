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

<fieldset id="froggyhistory-fieldset">
    <div class="panel">
        <div class="panel-heading">
        <legend><img src="{$froggyhistory.module_dir}logo.png" alt="" width="16" />{l s='Froggy History' mod='froggyhistory'}</legend>
        {l s='Product History' mod='froggyhistory'}
        </div>
        <ul id="froggyhistory-list">
            <li class="loader-gif"><img src="../modules/froggyhistory/views/img/loader.gif" /></li>
        </ul>
        <script>$(document).ready(function() { loadFroggyHistoryLog('{$froggyhistory.url}'); });</script>
    </div>
</fieldset>
