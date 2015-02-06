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

class FroggyHistoryLog extends ObjectModel
{
	public $id;

	/** @var integer Id Shop */
	public $id_shop;

	/** @var integer Id Employee */
	public $id_employee;

	/** @var integer Id Action Type */
	public $id_fhy_action;

	/** @var string Admin Object */
	public $admin_object;

	/** @var string Object */
	public $object;

	/** @var integer Id Object */
	public $id_object;

	/** @var string Module */
	public $module;

	/** @var string Diff */
	public $diff;

	/** @var string IP */
	public $ip;

	/** @var string Object creation date */
	public $date_add;

	/** @var Array action type */
	protected static $action_type_list = array();

	/** @var Array action type */
	protected static $action_register = array();

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'fhy_log',
		'primary' => 'id_fhy_log',
		'fields' => array(
			'id_shop' => 			array('type' => self::TYPE_INT, 'required' => true),
			'id_employee' => 		array('type' => self::TYPE_INT, 'required' => true),
			'id_fhy_action' => 		array('type' => self::TYPE_INT, 'required' => true),
			'admin_object' => 		array('type' => self::TYPE_STRING),
			'object' =>		 		array('type' => self::TYPE_STRING),
			'id_object' => 			array('type' => self::TYPE_INT),
			'module' => 			array('type' => self::TYPE_STRING),
			'diff' => 				array('type' => self::TYPE_STRING),
			'ip' => 				array('type' => self::TYPE_STRING),
			'date_add' => 			array('type' => self::TYPE_DATE),
		),
	);


	/**
	 * Return list of History Logs
	 */
	public static function getList($page = 1, $nb_per_page = 25, $admin_object = null, $object = null, $id_object = null, $id_employee = null, $id_shop = null)
	{
		$page = $page - 1;
		return Db::getInstance()->executeS('
			SELECT gl.*, ga.`name` as employee_action, e.`firstname`, e.`lastname`, s.`name` as shop_name
			FROM `'._DB_PREFIX_.'fhy_log` gl
			LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.`id_employee` = gl.`id_employee`)
			LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.`id_shop` = gl.`id_shop`)
			LEFT JOIN `'._DB_PREFIX_.'fhy_action` ga ON (ga.`id_fhy_action` = gl.`id_fhy_action`)
			WHERE 1
			'.($admin_object !== null ? 'AND gl.`admin_object` = \''.pSQL($admin_object).'\'' : '').'
			'.($object !== null ? 'AND gl.`object` = \''.pSQL($object).'\'' : '').'
			'.($id_object !== null ? 'AND gl.`id_object` = '.(int)$id_object : '').'
			'.($id_employee !== null ? 'AND gl.`id_employee` = '.(int)$id_employee : '').'
			'.($id_shop !== null ? 'AND gl.`id_shop` = '.(int)$id_shop : '').'
			ORDER BY `date_add` DESC
			LIMIT '.((int)$page * (int)$nb_per_page).','.(int)$nb_per_page.'
		');
	}

	/**
	 * Return total of History Logs
	 */
	public static function getTotal($admin_object = null, $object = null, $id_object = null, $id_employee = null, $id_shop = null)
	{
		return Db::getInstance()->getValue('
			SELECT COUNT(gl.`id_fhy_log`)
			FROM `'._DB_PREFIX_.'fhy_log` gl
			WHERE 1
			'.($admin_object !== null ? 'AND gl.`admin_object` = \''.pSQL($admin_object).'\'' : '').'
			'.($object !== null ? 'AND gl.`object` = \''.pSQL($object).'\'' : '').'
			'.($id_object !== null ? 'AND gl.`id_object` = '.(int)$id_object : '').'
			'.($id_employee !== null ? 'AND gl.`id_employee` = '.(int)$id_employee : '').'
			'.($id_shop !== null ? 'AND gl.`id_shop` = '.(int)$id_shop : '').'
		');
	}

	/**
	 * Return Action Type ID
	 */
	public static function getActionId($action)
	{
		if (count(self::$action_type_list) < 1)
		{
			$list = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'fhy_action`');
			foreach ($list as $l)
				self::$action_type_list[$l['name']] = $l['id_fhy_action'];
		}
		if (isset(self::$action_type_list[$action]))
			return self::$action_type_list[$action];
		return 0;
	}


	/**
	 * Check if action has already been registered / logged during this page call
	 */

	public static function addActionRegister($object, $id_object, $id_history_log)
	{
		self::$action_register[(string)$object.'-'.(int)$id_object] = (int)$id_history_log;
		self::$action_register[(string)$object.'-0'] = (int)$id_history_log;
		return true;
	}

	public static function getActionRegister($object, $id_object)
	{
		if (isset(self::$action_register[(string)$object.'-'.(int)$id_object]))
			return (int)self::$action_register[(string)$object.'-'.(int)$id_object];
		return 0;
	}
}