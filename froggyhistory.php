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
if (!class_exists('FroggyModule', false)) require_once __DIR__.'/froggy/FroggyModule.php';

// Require
require_once(dirname(__FILE__).'/classes/FroggyHistoryLog.php');

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
		return $this->display(__FILE__, 'getcontent.tpl');
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
			$history_log_list[$k]['sentence'] = $this->writeLogSentence($log);
	
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
	 * Write log sentence
	 * Uses in order to display understable sentence describing employees log
	 *
	 * @param string $log
	 */
	public function writeLogSentence($log)
	{
		// Matching array
		$match_action = array(
			'ADD' => $this->l('created'),
			'UPDATE' => $this->l('updated'),
			'DELETE' => $this->l('deleted'),
			'DUPLICATE' => $this->l('duplicated'),
		);
		$match_object_translation = array(
			'Address' => $this->l('the address'),
			'Employee' => $this->l('the employee'),
			'Configuration' => $this->l('the configuration'),
			'Product' => $this->l('the product'),
			'ProductSupplier' => $this->l('the product supplier'),
			'Category' => $this->l('the category'),
			'Supplier' => $this->l('the supplier'),
			'Manufacturer' => $this->l('the manufacturer'),
			'Language' => $this->l('the language'),
			'AdminProductsController' => $this->l('the product'),
			'AdminCategoriesController' => $this->l('the category'),
			'AdminSuppliersController' => $this->l('the supplier'),
			'AdminManufacturersController' => $this->l('the manufacturer'),
			'AdminPreferencesController' => $this->l('the preference'),
		);
		$match_object_nolink = array('Configuration', 'ProductSupplier');
		$match_multilang_object = array('Product', 'Category', 'Supplier', 'Manufacturer');

		// Splitting the date
		$year = substr($log['date_add'], 0, 4);
		$month = substr($log['date_add'], 5, 2);
		$day = substr($log['date_add'], 8, 2);
		$hour = substr($log['date_add'], 11, 8);

		// Init var
		$token_admin_employees = Tools::getAdminToken('AdminEmployees'.(int)Tab::getIdFromClassName('AdminEmployees').(int)$this->ajax_id_employee);
		$employee = '<a href="index.php?controller=adminemployees&id_employee='.(int)$log['id_employee'].'&updateemployee&token='.$token_admin_employees.'" target="_blank">'.$log['firstname'].' '.$log['lastname'].' (ID #'.$log['id_employee'].')</a>';

		// Get object translation
		if (isset($match_object_translation[$log['object']]))
			$object_translation = $match_object_translation[$log['object']];
		if (isset($match_object_translation[$log['admin_object']]) && !isset($object_translation))
			$object_translation = $match_object_translation[$log['admin_object']];
		if (!isset($object_translation))
			$object_translation = $this->l('the').' '.strtolower($log['object']);

		// Building the sentence
		$sentence = array();
		$sentence['hour'] = $this->l('%1$s-%2$s-%3$s %4$s');
		$sentence['description'] = $this->l('Employee %1$s %2$s %3$s');
		if ((int)$log['id_object'] > 0)
		{
			// Get admin token to build link to dynamic sentence
			$controller_name = str_replace('Controller', '', $log['admin_object']);
			$token_admin = Tools::getAdminToken($controller_name.(int)Tab::getIdFromClassName($controller_name).(int)$this->ajax_id_employee);

			// Check if we build link for this type of object			
			if (!in_array($log['object'], $match_object_nolink))
				$object_translation = '<a href="index.php?controller='.strtolower($controller_name).'&'.($log['object']::$definition['primary']).'='.(int)$log['id_object'].'&update'.strtolower($log['object']::$definition['table']).'&token='.$token_admin.'" target="_blank">'.$object_translation;

			// Try to load object to retrieve the name
			// Specific case for Product Object where Id Lang is the third param, sigh :'(
			if ($log['object'] == 'Product')
				$object_load = new $log['object']((int)$log['id_object'], true, Tools::getValue('id_lang'));
			else if (in_array($log['object'], $match_multilang_object))
				$object_load = new $log['object']((int)$log['id_object'], true, Tools::getValue('id_lang'));
			else
				$object_load = new $log['object']((int)$log['id_object']);
			
			if (isset($object_load->name) && !empty($object_load->name) && !is_array($object_load->name))
				$object_translation .= ' "'.$object_load->name.'"';
			if (isset($object_load->title) && !empty($object_load->title) && !is_array($object_load->title) && !isset($object_load->name))
				$object_translation .= ' "'.$object_load->title.'"';

			// Writing Object ID 
			$object_translation .= ' ID #'.(int)$log['id_object'];
			
			// Check if we build link for this type of object
			if (!in_array($log['object'], $match_object_nolink))
				$object_translation .= '</a>';
		}

		// Hour and description translations
		$sentence['hour'] = sprintf($sentence['hour'], $year, $month, $day, $hour);
		$sentence['description'] = sprintf($sentence['description'], $employee, $match_action[$log['employee_action']], $object_translation);

		return $sentence;
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


	/**
	 * Hook Log Action
	 * Uses in order to log employee actions on the back office
	 *
	 * @param $action, $object, $controller
	 * @return boolean log result
	 */
	public function hookLog($action, $object = null, $controller = null)
	{
		// Check
		if (!isset($this->context->employee->id))
			return true;
	
		// Init
		$id_object = 0;
		$class_name = '';
		$object = (isset($object['object']) ? $object['object'] : null);
		$controller = (isset($controller['controller']) ? $controller['controller'] : null);
		$controller_name = ($controller !== null ? get_class($controller) : '');
		if (empty($controller_name))
		{
			$controllers = Dispatcher::getControllers(array(_PS_ADMIN_DIR_.'/tabs/', _PS_ADMIN_CONTROLLER_DIR_));
			if (isset($controllers[strtolower(Tools::getValue('controller'))]))
				$controller_name = $controllers[strtolower(Tools::getValue('controller'))];
		}

		// Check if object exists and different from FroggyHistoryLog class to avoid infinite loop
		if ($object !== null && get_class($object) == 'FroggyHistoryLog')
			return true;

		// Check status ADD / UPDATE
		if ($action == 'ADD/UPDATE')
		{
			$action = 'UPDATE';
			foreach ($_GET as $k => $v)
				if (substr($k, 0, 9) == 'submitAdd')
					$action = 'ADD';
		}

		// Retrieve class object model from controller class and id_object from GET var
		if ($controller !== null)
		{
			$class_name = $controller->className;
			$id_object = Tools::getValue($class_name::$definition['primary']);
		}

		// Retrieve class object model and id_object if object is not null
		if ($object !== null)
		{
			$class_name = get_class($object);
			$id_object = (int)$object->id;
		}

		// Check if a log has already been saved on this object during this page call
		$id_history_log = FroggyHistoryLog::getActionRegister($class_name, $id_object);

		// If yes, we load the log and complete it, if no we create a new log
		if ((int)$id_history_log > 0)
			$history_log = new FroggyHistoryLog((int)$id_history_log);
		else
			$history_log = new FroggyHistoryLog();

		// We fill the history log object
		$history_log->id_shop = (int)$this->context->shop->id;
		$history_log->id_employee = (isset($this->context->employee->id) ? (int)$this->context->employee->id : (int)$history_log->id_employee);
		$history_log->id_fhy_action = ((int)$history_log->id_fhy_action < 1 ? (int)FroggyHistoryLog::getActionId($action) : (int)$history_log->id_fhy_action);
		$history_log->id_shop = (int)$this->context->shop->id;
		$history_log->admin_object = pSQL($controller_name);
		$history_log->object = pSQL($class_name);
		$history_log->id_object = ((int)$id_object > 0 ? (int)$id_object : (int)$history_log->id_object);
		$history_log->module = pSQL(Tools::getValue('module'));
		$history_log->ip = pSQL(Tools::getRemoteAddr());

		// If log already exists, we update it
		if ((int)$id_history_log > 0)
			return $history_log->update();

		// If not we create it and save it in the page call register
		if ($history_log->add())
		{
			FroggyHistoryLog::addActionRegister($class_name, $id_object, (int)$history_log->id);
			return true;
		}
		return false;
	}

	public function hookActionObjectAddAfter($object) { return $this->hookLog('ADD', $object); }
	public function hookActionObjectUpdateAfter($object) { return $this->hookLog('UPDATE', $object); }
	public function hookActionObjectDeleteAfter($object) { return $this->hookLog('DELETE', $object); }
	public function hookActionAdminEditAfter($controller) { return $this->hookLog('UPDATE', null, $controller); }
	public function hookActionAdminUpdateAfter($controller) { return $this->hookLog('UPDATE', null, $controller); }
	public function hookActionAdminSaveAfter($controller) { return $this->hookLog('ADD/UPDATE', null, $controller); }
	public function hookActionAdminDeleteBefore($controller) { return $this->hookLog('DELETE', null, $controller); }
	public function hookActionAdminDuplicateAfter($controller) { return $this->hookLog('DUPLICATE', null, $controller); }
}
