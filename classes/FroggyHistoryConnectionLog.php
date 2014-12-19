<?php
/**
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
 * @author    Froggy Commerce <contact@froggy-commerce.com>
 * @copyright 2013-2014 Froggy Commerce
 * @license   Unauthorized copying of this file, via any medium is strictly prohibited
 */

/*
 * Security
 */
defined('_PS_VERSION_') || require dirname(__FILE__).'/index.php';


class FroggyHistoryConnectionLog extends ObjectModel
{
	public $id;

	/** @var integer Id Shop */
	public $id_shop;

	/** @var integer Id Employee */
	public $id_employee;

	/** @var string Browser */
	public $browser;

	/** @var string IP */
	public $ip;

	/** @var string Object creation date */
	public $date_add;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'fhy_connection_log',
		'primary' => 'id_fhy_connection_log',
		'fields' => array(
			'id_shop' => 			array('type' => self::TYPE_INT, 'required' => true),
			'id_employee' => 		array('type' => self::TYPE_INT, 'required' => true),
			'browser' => 		array('type' => self::TYPE_STRING),
			'ip' => 				array('type' => self::TYPE_STRING),
			'date_add' => 			array('type' => self::TYPE_DATE),
		),
	);

	/**
	 * Return list of History Logs
	 */
	public static function getList($page = 1, $nb_per_page = 25, $ip = null, $id_employee = null, $id_shop = null)
	{
		$page = $page - 1;
		return Db::getInstance()->executeS('
			SELECT gcl.*, e.`firstname`, e.`lastname`, s.`name` as shop_name
			FROM `'._DB_PREFIX_.'fhy_connection_log` gcl
			LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.`id_employee` = gcl.`id_employee`)
			LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.`id_shop` = gcl.`id_shop`)
			WHERE 1
			'.($ip !== null ? 'AND gcl.`ip` = \''.pSQL($ip).'\'' : '').'
			'.($id_employee !== null ? 'AND gcl.`id_employee` = '.(int)$id_employee : '').'
			'.($id_shop !== null ? 'AND gcl.`id_shop` = '.(int)$id_shop : '').'
			ORDER BY gcl.`date_add` DESC
			LIMIT '.((int)$page * (int)$nb_per_page).','.(int)$nb_per_page.'
		');
	}
}