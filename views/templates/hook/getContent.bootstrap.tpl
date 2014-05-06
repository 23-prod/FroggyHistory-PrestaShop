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
    <legend><img src="{$froggyhistory.module_dir}logo.png" alt="" width="16" />{l s='Froggy History' mod='froggyhistory'}</legend>

    <div id="froggyhistory-introduction">
        <h3>{l s='Introduction' mod='froggyhistory'}</h3>
        <p>{l s='You will find, below, the history of employees actions on the administration panel.' mod='froggyhistory'}</p>
        <p>
        {l s='You can also see specific history in:' mod='froggyhistory'}<br>
            - {l s='on the product administration forms:' mod='froggyhistory'} {l s='A tab named "Froggy History" should have appeared. It will contain most of the actions made on the product since the module was installed.' mod='froggyhistory'}<br>
            - {l s='on the employee administration forms:' mod='froggyhistory'} ({l s='A section named "Froggy History" should have appeared. It will contain most of the actions the employee made since the module was installed.' mod='froggyhistory'})<br>
        </p>

        <p><b>The standard Lorem Ipsum passage, used since the 1500s</b></p>
        <p>"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."</p>
        <p><b>Section 1.10.32 of "de Finibus Bonorum et Malorum", written by Cicero in 45 BC</b></p>
        <p>"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?"</p>
    </div>

    <div id="froggyhistory-configuration">
        <form>
            <h3>{l s='Configuration' mod='froggyhistory'}</h3>
            <label>{l s='Delete history older than:' mod='froggyhistory'}</label>
            <input type="text" name="FROGGY_HISTORY_DELETE" value="30" /> {l s='days:' mod='froggyhistory'}
            <br><br>
            <label>{l s='Archive deleted history in log files:' mod='froggyhistory'}</label>
            <input type="checkbox" name="FROGGY_HISTORY_ARCHIVE" />
            <br><br>
            <p align="center"><input type="submit" class="button" name="froggyhistory-submit" id="froggyhistory-submit" value="{l s='Validate' mod='froggyhistory'}" /></p>
        </form>

        <br><br><br><br>
        <p align="center"><input type="button" class="button" id="froggyhistory-see-general-history" value="{l s='See general history' mod='froggyhistory'}" /></p>
    </div>

    <div id="froggyhistory-general">
        <h3>{l s='General History' mod='froggyhistory'}</h3>
        <div id="froggyhistory-list">
            <p align="center"><img src="../modules/froggyhistory/views/img/loader.gif" /></p>
        </div>
    </div>

    <script>$(document).ready(function() { loadFroggyHistoryLog('{$froggyhistory.url}'); });</script>
</fieldset>
