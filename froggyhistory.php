<?php
/**
 * 2013-2015 Froggy Commerce
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
 * @author    Froggy Commerce <contact@froggy-commerce.com>
 * @copyright 2013-2015 Froggy Commerce
 * @license   Unauthorized copying of this file, via any medium is strictly prohibited
 */

/*
 * Security
 */
defined('_PS_VERSION_') || require dirname(__FILE__).'/index.php';

/*
 * Include Froggy Library
 */
if (!class_exists('FroggyModule', false)) {
    require_once dirname(__FILE__).'/froggy/FroggyModule.php';
}

/*
 * Requires
 */
require_once(dirname(__FILE__).'/classes/FroggyHistoryLibrary.php');
require_once(dirname(__FILE__).'/classes/FroggyHistoryLog.php');
require_once(dirname(__FILE__).'/classes/FroggyHistoryObjectLog.php');
require_once(dirname(__FILE__).'/classes/FroggyHistoryConnectionLog.php');


class FroggyHistory extends FroggyModule
{
    /**
     * @var string Ajax Secure Key
     */
    public $ajax_secure_key;

    /**
     * @var integer Ajax Id Employee
     */
    public $ajax_id_employee;

    /**
     * @var array contains error form postProcess()
     */
    protected $errors = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'froggyhistory';
        $this->author = 'Froggy Commerce';
        $this->version = '1.1.0';
        $this->tab = 'administration';

        parent::__construct();

        $this->displayName = $this->l('Froggy History');
        $this->description = $this->l('Allow you to know the different actions of each employee in the Back Office');

        // Make secure key impossible to find if id_employee is not defined :)
        $this->ajax_id_employee = rand();
        if ((int)Tools::getValue('ajax_id_employee') > 0) {
            $this->ajax_id_employee = (int)Tools::getValue('ajax_id_employee');
        }
        if (isset($this->context->employee->id) && (int)$this->context->employee->id > 0) {
            $this->ajax_id_employee = (int)$this->context->employee->id;
        }

        // Generate ajax security token
        $this->ajax_secure_key = md5(date('Y-m-d')._COOKIE_KEY_.$this->name.$this->ajax_id_employee);
        $this->module_key = '95a3eaf63abacc67f34e784a42040cd6';
    }


    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        if (Tools::getValue('froggyhistory-submit') != '') {
            Configuration::updateValue('FH_DELETE_AFTER', (int)Tools::getValue('FH_DELETE_AFTER'));
            Configuration::updateValue('FH_LOG_DELETED', (Tools::getValue('FH_LOG_DELETED') != '' ? 1 : 0));
            $confirm = 'ok';
        }

        $url = '?ajax_secure_key='.htmlentities($this->ajax_secure_key);
        $url .= '&ajax_id_employee='.(int)$this->ajax_id_employee.'&id_lang='.(int)$this->context->cookie->id_lang;
        $assign = array(
            'module_dir' => $this->_path,
            'ps_version' => Tools::substr(_PS_VERSION_, 0, 3),
            'url' => $url,
            'FH_DELETE_AFTER' => Configuration::get('FH_DELETE_AFTER'),
            'FH_LOG_DELETED' => Configuration::get('FH_LOG_DELETED'),
            'archives_directory' => dirname(__FILE__).'/archives/',
            'archives_directory_is_writable' => is_writable(dirname(__FILE__).'/archives/'),
            'confirm' => (isset($confirm) ? $confirm : ''),
        );

        $this->smarty->assign($this->name, $assign);
        return $this->fcdisplay(__FILE__, 'getContent.tpl');
    }


    /**
     * Return the current url of page
     * @return string
     */
    protected function getCurrentUrl()
    {
        $url = Tools::htmlentitiesUTF8(
            $this->context->link->protocol_link .
            $_SERVER['SERVER_NAME'] .
            $_SERVER['REQUEST_URI']
        );
        return str_replace('&amp;', '&', $url);
    }


    /**
     * Ajax Request Dispatcher
     *
     */
    public function ajaxRequest()
    {
        // Init only for PrestaShop Validator :p
        $page = $admin_object = $object = $id_object = $id_employee = $id_shop = null;

        $url = '?ajax_secure_key='.htmlentities($this->ajax_secure_key);
        $url .= '&ajax_id_employee='.(int)$this->ajax_id_employee;
        $var_list = array('page', 'admin_object', 'object', 'id_object', 'id_employee', 'id_shop');
        foreach ($var_list as $var) {
            $$var = null;
            if (Tools::getValue($var) != '') {
                $$var = pSQL(Tools::getValue($var));
            }
            if ($var != 'page' && $var !== null) {
                $url .= '&'.$var.'='.$$var;
            }
        }
        if ((int)$page < 1) {
            $page = 1;
        }
        $nb_per_page = 25;

        $history_log_list = FroggyHistoryLog::getList((int)$page, $nb_per_page, $admin_object, $object, $id_object, $id_employee, $id_shop);
        foreach ($history_log_list as $k => $log) {
            $history_log_list[$k]['sentence'] = FroggyHistoryLibrary::getLib($this)->writeLogSentence($log);
        }

        $total = FroggyHistoryLog::getTotal($admin_object, $object, $id_object, $id_employee, $id_shop);
        $nb_pages = ceil($total / $nb_per_page);
        $assign = array(
            'page' => (int)$page,
            'nb_pages' => (int)$nb_pages + 1,
            'url' => htmlentities($url),
            'history_log_list' => $history_log_list,
        );
        $this->smarty->assign($this->name, $assign);

        // Display template
        echo $this->fcdisplay(__FILE__, 'ajax.tpl');
    }


    public function saveConnectionLog()
    {
        if ($this->context->cookie->fhy_connection_log_md5 != md5($this->context->cookie->fhy_connection_log_id.$this->context->cookie->date_add)) {
            $connection_log = new FroggyHistoryConnectionLog();
            $connection_log->id_employee = (int)$this->context->employee->id;
            $connection_log->id_shop = (int)$this->context->shop->id;
            $connection_log->browser = pSQL($_SERVER['HTTP_USER_AGENT']);
            $connection_log->ip = Tools::getRemoteAddr();
            $connection_log->add();
            $this->context->cookie->fhy_connection_log_id = $connection_log->id;
            $this->context->cookie->fhy_connection_log_md5 = md5($connection_log->id.$this->context->cookie->date_add);
            Hook::exec('ActionFroggyHistoryEmployeeConnection');
        }
    }

    public $quantity_cache;

    public function storePreviousQuantityOnUpdate()
    {
        if (Tools::getIsset('actionQty')) {
            $idp = (int)Tools::getValue('id_product');
            $idpa = (int)Tools::getValue('id_product_attribute');
            $this->quantity_cache[$idp][$idpa] = StockAvailable::getQuantityAvailableByProduct($idp, $idpa);
        }
    }

    /**
     * Hook Action Admin Controller SetMedia
     * Uses in order to add CSS file in backend
     *
     * @param $params
     */
    public function hookActionAdminControllerSetMedia($params)
    {
        $this->saveConnectionLog();
        $this->storePreviousQuantityOnUpdate();

        $controller = Tools::strtolower(Tools::getValue('controller'));
        if (($controller == 'adminmodules' && Tools::getValue('configure') == $this->name)
            || $controller == 'adminemployees' || $controller == 'adminproducts'
        ) {
            $this->context->controller->addCSS($this->_path.'views/css/froggyhistory'.(isset($this->bootstrap) && $this->bootstrap ? '.bootstrap' : '').'.css');
            $this->context->controller->addJS($this->_path.'views/js/froggyhistory.js');
        }
    }

    /**
     * Hook Display Admin Employee Form
     * Uses in order to display employee actions on the back office
     *
     * @param $params
     * @return string display for this hook
     */
    public function hookDisplayAdminEmployeesForm($params)
    {
        $url = '?ajax_secure_key='.htmlentities($this->ajax_secure_key).'&id_employee='.(int)Tools::getValue('id_employee');
        $url .= '&ajax_id_employee='.(int)$this->ajax_id_employee.'&id_lang='.(int)$this->context->cookie->id_lang;
        $assign = array('module_dir' => $this->_path, 'url' => $url);
        $this->smarty->assign($this->name, $assign);
        return $this->fcdisplay(__FILE__, 'hookDisplayAdminEmployeesForm.tpl');
    }

    /**
     * Hook Display Admin Products Extra
     * Uses in order to display employee actions on the product
     *
     * @param $params
     * @return string display for this hook
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = Tools::getValue('id_product');
        if (isset($params['id_product']) && $params['id_product'] > 0) {
            $id_product = $params['id_product'];
        }

        $url = '?ajax_secure_key='.htmlentities($this->ajax_secure_key).'&object=Product&id_object='.(int)$id_product;
        $url .= '&ajax_id_employee='.(int)$this->ajax_id_employee.'&id_lang='.(int)$this->context->cookie->id_lang;
        $assign = array('module_dir' => $this->_path, 'url' => $url);
        $this->smarty->assign($this->name, $assign);
        return $this->fcdisplay(__FILE__, 'hookDisplayAdminProductsExtra.tpl');
    }


    public function hookActionObjectAddAfter($object)
    {
        return FroggyHistoryLibrary::getLib($this)->hookLog('ADD', $object);
    }

    public function hookActionObjectUpdateAfter($object)
    {
        return FroggyHistoryLibrary::getLib($this)->hookLog('UPDATE', $object);
    }

    public function hookActionObjectDeleteAfter($object)
    {
        return FroggyHistoryLibrary::getLib($this)->hookLog('DELETE', $object);
    }

    public function hookActionAdminDuplicateAfter($controller)
    {
        return FroggyHistoryLibrary::getLib($this)->hookLog('DUPLICATE', null, $controller);
    }

    public function hookActionUpdateQuantity($params)
    {
        // Build params
        $product = new Product((int)Tools::getValue('id_product'));
        $object = array('object' => $product);

        // Log action
        $id_history_log = (int)FroggyHistoryLibrary::getLib($this)->hookLog('UPDATE', $object);

        // Update log history
        if ($id_history_log > 0 && version_compare(_PS_VERSION_, '1.7.0') < 0) {

            // Get quantity cached
            $idp = (int)Tools::getValue('id_product');
            $idpa = (int)Tools::getValue('id_product_attribute');
            if (!isset($this->quantity_cache[$idp][$idpa])) {
                $this->quantity_cache[$idp][$idpa] = '?';
            }

            // Build diff

            $diff = [];

            $field = 'quantity';
            if ($idpa > 0) {
                $field = 'quantity ('.$this->l('combination').' #'.$idpa.')';
            }
            $diff[$field] = array('before' => $this->quantity_cache[$idp][$idpa], 'after' => Tools::getValue('value'));

            // Update
            $history_log = new FroggyHistoryLog((int)$id_history_log);
            $history_log->diff = Tools::jsonEncode($diff);
            $history_log->update();
        }

        // Return result (logged or not)
        return $id_history_log;
    }
}
