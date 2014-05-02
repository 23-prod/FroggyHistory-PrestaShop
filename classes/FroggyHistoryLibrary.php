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

class FroggyHistoryLibrary
{
	public $module;
	public $ajax_id_employee;


	/**
	 * Constructor
	 * @param $module
	 */
	public function __construct($module)
	{
		$this->module = $module;
		$this->ajax_id_employee = $this->module->ajax_id_employee;
	}


	/**
	 * Library Factory
	 * @param $module
	 * @return FroggyHistoryLibrary
	 */
	public static function getLib($module)
	{
		return new FroggyHistoryLibrary($module);
	}

	/**
	 * Translation method
	 * @param $string
	 * @return mixed
	 */
	public function l($string)
	{
		return $this->module->l($string);
	}

	/**
	 * Get Object WhiteList
	 * @return array
	 */
	public function getObjectsWhiteList()
	{
		return array(
			'Address' => $this->l('the address'),
			'Attribute' => $this->l('the attribute'),
			'AttributeGroup' => $this->l('the attribute group'),
			'Carrier' => $this->l('the carrier'),
			'Cart' => $this->l('the cart'),
			'CartRule' => $this->l('the cart rule'),
			'Category' => $this->l('the category'),
			'CMS' => $this->l('the CMS page'),
			'CMSCategory' => $this->l('the CMS category'),
			'Configuration' => $this->l('the configuration'),
			'Contact' => $this->l('the contact'),
			'Country' => $this->l('the country'),
			'County' => $this->l('the county'),
			'Currency' => $this->l('the currency'),
			'Customer' => $this->l('the customer'),
			'CustomerMessage' => $this->l('the customer message'),
			'CustomerThread' => $this->l('the customer thread'),
			'Discount' => $this->l('the discount'),
			'Employee' => $this->l('the employee'),
			'Feature' => $this->l('the feature'),
			'FeatureValue' => $this->l('the feature value'),
			'Group' => $this->l('the customer group'),
			'GroupReduction' => $this->l('the customer group reduction'),
			'Guest' => $this->l('the guest'),
			'Language' => $this->l('the language'),
			'Manufacturer' => $this->l('the manufacturer'),
			'Order' => $this->l('the order'),
			'Product' => $this->l('the product'),
			'State' => $this->l('the state'),
			'Store' => $this->l('the store'),
			'Supplier' => $this->l('the supplier'),
			'Zone' => $this->l('the geographical zone'),
			'AdminProductsController' => $this->l('the product'),
			'AdminCategoriesController' => $this->l('the category'),
			'AdminSuppliersController' => $this->l('the supplier'),
			'AdminManufacturersController' => $this->l('the manufacturer'),
			'AdminPreferencesController' => $this->l('the preference'),
		);
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
		$match_object_translation = $this->getObjectsWhiteList();
		$match_object_nolink = array('Configuration');
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
			if (!in_array($log['object'], $match_object_nolink) && in_array($log['object'], $match_multilang_object))
				$object_translation = '<a href="index.php?controller='.strtolower($controller_name).'&'.($log['object']::$definition['primary']).'='.(int)$log['id_object'].'&update'.strtolower($log['object']::$definition['table']).'&token='.$token_admin.'" target="_blank">'.$object_translation;

			// Try to load object to retrieve the name
			// Specific case for Product Object where Id Lang is the third param, sigh :'(
			if ($log['object'] == 'Product')
				$object_load = new $log['object']((int)$log['id_object'], true, Tools::getValue('id_lang'));
			else if (in_array($log['object'], $match_multilang_object))
				$object_load = new $log['object']((int)$log['id_object'], true, Tools::getValue('id_lang'));
			else
				if (class_exists($log['object']))
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
		if ($log['diff'] != '')
			$sentence['diff'] = json_decode(stripslashes($log['diff']), true);

		return $sentence;
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
		$objects_white_list = $this->getObjectsWhiteList();
		if ($object !== null && !isset($objects_white_list[get_class($object)]))
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

		// Retrieve the object history and compare it
		$diff = '';
		if ($class_name != '')
		{
			$new_object = new $class_name((int)$id_object);
			$id_fhy_object_log = FroggyHistoryObjectLog::getHistoryObjectLogId($class_name, $id_object);
			if ($id_fhy_object_log > 0)
			{
				$history_object = new FroggyHistoryObjectLog((int)$id_fhy_object_log);
				$diff = $history_object->getDiff($new_object);
				$history_object->delete();
			}

			$history_object = new FroggyHistoryObjectLog();
			$history_object->id_object = $id_object;
			$history_object->object = $class_name;
			$history_object->data = json_encode($new_object);
			$history_object->add();
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
		$history_log->diff = pSQL($diff);
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
}