<?php
/*
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

// Security
defined('_PS_VERSION_') || require dirname(__FILE__).'/index.php';

// Include Froggy Library
if (!class_exists('FroggyModule', false)) require_once dirname(__FILE__).'/froggy/FroggyModule.php';

// Require
require_once(dirname(__FILE__).'/classes/FroggyHistoryLibrary.php');
require_once(dirname(__FILE__).'/classes/FroggyHistoryLog.php');
require_once(dirname(__FILE__).'/classes/FroggyHistoryObjectLog.php');

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
		parent::__construct();

		$this->displayName = $this->l('Froggy History');
		$this->description = $this->l('Allow you to know the different actions of each employee in the Back Office');

		// Make secure key impossible to find if id_employee is not defined :)
		$this->ajax_id_employee = rand();
		if ((int)Tools::getValue('ajax_id_employee') > 0)
			$this->ajax_id_employee = (int)Tools::getValue('ajax_id_employee');
		if (isset($this->context->employee->id) && (int)$this->context->employee->id > 0)
			$this->ajax_id_employee = (int)$this->context->employee->id;

		// Generate ajax security token
		$this->ajax_secure_key = md5(date('Y-m-d')._COOKIE_KEY_.$this->name.$this->ajax_id_employee);
	}


	/**
	 * {@inheritdoc}
	 */
	public function getContent()
	{
		$url = '?ajax_secure_key='.htmlentities($this->ajax_secure_key);
		$url .= '&ajax_id_employee='.(int)$this->ajax_id_employee.'&id_lang='.(int)$this->context->cookie->id_lang;
		$assign = array('module_dir' => $this->_path, 'url' => $url);
		$this->smarty->assign($this->name, $assign);
		return $this->display(__FILE__, 'getContent.tpl');
	}


	/**
	 * Return the current url of page
	 * @return string
	 */
	protected function getCurrentUrl()
	{
		$url = Tools::htmlentitiesUTF8($this->context->link->protocol_link.
			$_SERVER['SERVER_NAME'].
			$_SERVER['REQUEST_URI']);
		return str_replace('&amp;', '&', $url);
	}


	/**
	 * Ajax Request Dispatcher
	 *
	 */
	public function ajaxRequest()
	{	
		$url = '?ajax_secure_key='.htmlentities($this->ajax_secure_key);
		$url .= '&ajax_id_employee='.(int)$this->ajax_id_employee;
		$var_list = array('page', 'admin_object', 'object', 'id_object', 'id_employee', 'id_shop');
		foreach ($var_list as $var)
		{
			$$var = NULL;
			if (Tools::getValue($var) != '')
				$$var = pSQL(Tools::getValue($var));
			if ($var != 'page' && $var !== NULL)
				$url .= '&'.$var.'='.$$var;
		}
		if ((int)$page < 1)
			$page = 1;
		$nb_per_page = 25;
	
		$history_log_list = FroggyHistoryLog::getList((int)$page, $nb_per_page, $admin_object, $object, $id_object, $id_employee, $id_shop);
		foreach ($history_log_list as $k => $log)
			$history_log_list[$k]['sentence'] = FroggyHistoryLibrary::getLib($this)->writeLogSentence($log);
	
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
		echo $this->display(__FILE__, 'ajax.tpl');
	}




	/**
	 * Hook Action Admin Controller SetMedia
	 * Uses in order to add CSS file in backend
	 *
	 * @param $params
	 */
	public function hookActionAdminControllerSetMedia($params)
	{
		$controller = strtolower(Tools::getValue('controller'));
		if (($controller == 'adminmodules' && Tools::getValue('configure') == $this->name)
			|| $controller == 'adminemployees' || $controller == 'adminproducts')
		{
			$this->context->controller->addCSS($this->_path.'views/css/froggyhistory.css');
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
		return $this->display(__FILE__, 'hookDisplayAdminEmployeesForm.tpl');
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
		$url = '?ajax_secure_key='.htmlentities($this->ajax_secure_key).'&admin_object=AdminProductsController&id_object='.(int)Tools::getValue('id_product');
		$url .= '&ajax_id_employee='.(int)$this->ajax_id_employee.'&id_lang='.(int)$this->context->cookie->id_lang;
		$assign = array('module_dir' => $this->_path, 'url' => $url);
		$this->smarty->assign($this->name, $assign);
		return $this->display(__FILE__, 'hookDisplayAdminProductsExtra.tpl');
	}


	public function hookActionObjectAddAfter($object) { return FroggyHistoryLibrary::getLib($this)->hookLog('ADD', $object); }
	public function hookActionObjectUpdateAfter($object) { return FroggyHistoryLibrary::getLib($this)->hookLog('UPDATE', $object); }
	public function hookActionObjectDeleteAfter($object) { return FroggyHistoryLibrary::getLib($this)->hookLog('DELETE', $object); }

	public function hookActionAdminDuplicateAfter($controller) { return FroggyHistoryLibrary::getLib($this)->hookLog('DUPLICATE', null, $controller); }
}
