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

<fieldset id="froggyhistory_fieldset">
	<legend><img src="{$froggyhistory.module_dir}logo.png" alt="" width="16" />{l s='Froggy History' mod='froggyhistory'}</legend>

	<h3>{l s='Introduction' mod='froggyhistory'}</h3>
    <p>{l s='You will find, below, the history of employees actions on the administration panel.' mod='froggyhistory'}</p>
    <p>
        {l s='You can also see specific history in:' mod='froggyhistory'}<br>
        - {l s='on the product administration forms:' mod='froggyhistory'} {l s='A tab named "Froggy History" should have appeared. It will contain most of the actions made on the product since the module was installed.' mod='froggyhistory'}<br>
        - {l s='on the employee administration forms:' mod='froggyhistory'} ({l s='A section named "Froggy History" should have appeared. It will contain most of the actions the employee made since the module was installed.' mod='froggyhistory'})<br>
    </p>

    <h3>{l s='General History' mod='froggyhistory'}</h3>
    <div id="froggyhistory_list">
		<p align="center"><img src="../modules/froggyhistory/views/img/loader.gif" /></p>
	</div>

    <script>$(document).ready(function() { loadFroggyHistoryLog('{$froggyhistory.url}'); });</script>
</fieldset>
