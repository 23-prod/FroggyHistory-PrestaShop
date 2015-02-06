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

class FroggyHistoryObjectLog extends ObjectModel
{
	public $id;

	/** @var integer Id Object */
	public $id_object;

	/** @var string Object */
	public $object;

	/** @var string Data */
	public $data;

	/** @var Array action type */
	protected static $action_type_list = array();

	/** @var Array action type */
	protected static $action_register = array();

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'fhy_object_log',
		'primary' => 'id_fhy_object_log',
		'fields' => array(
			'id_object' => 			array('type' => self::TYPE_INT, 'required' => true),
			'object' =>		 		array('type' => self::TYPE_STRING),
			'data' => 				array('type' => self::TYPE_STRING),
		),
	);

	public function getDiff($new_object)
	{
		$diff_list = array();
		$old_object = Tools::jsonDecode($this->data, true);
		foreach ($new_object as $field => $value)
		{
			if (is_array($value))
			{
				foreach ($value as $id_lang => $value_lang)
				{
					$lang = new Language((int)$id_lang);
					$iso_lang = Tools::strtoupper($lang->iso_code);
					if (isset($old_object[$field][$id_lang]) && strip_tags($old_object[$field][$id_lang]) != strip_tags($value_lang))
						$diff_list[Tools::ucfirst($field)][$iso_lang] = array('before' => strip_tags($old_object[$field][$id_lang]), 'after' => strip_tags($value_lang));
				}
			}
			else
			{
				if (isset($old_object[$field]) && $field != 'date_upd' && $old_object[$field] != $value)
					$diff_list[Tools::ucfirst($field)] = array('before' => $old_object[$field], 'after' => $value);
			}
		}

		if (count($diff_list))
			return Tools::jsonEncode($diff_list);
		return '';
	}

	public static function getHistoryObjectLogId($object, $id_object)
	{
		return Db::getInstance()->getValue('
		SELECT `id_fhy_object_log`
		FROM `'._DB_PREFIX_.'fhy_object_log`
		WHERE `object` = \''.pSQL($object).'\'
		AND `id_object` = '.(int)$id_object);
	}
}