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

class FroggyContext
{
	/**
	 * @var Context
	 */
	protected static $instance;

	/**
	 * @var Cart
	 */
	public $cart;

	/**
	 * @var Customer
	 */
	public $customer;

	/**
	 * @var Cookie
	 */
	public $cookie;

	/**
	 * @var Link
	 */
	public $link;

	/**
	 * @var Country
	 */
	public $country;

	/**
	 * @var Employee
	 */
	public $employee;

	/**
	 * @var Controller
	 */
	public $controller;

	/**
	 * @var Language
	 */
	public $language;

	/**
	 * @var Currency
	 */
	public $currency;

	/**
	 * @var AdminTab
	 */
	public $tab;

	/**
	 * @var Shop
	 */
	public $shop;

	/**
	 * @var Smarty
	 */
	public $smarty;

	/**
	 * @var boolean|string mobile device of the customer
	 */
	protected $mobile_device;

	public function __construct()
	{
		$this->tab = null;

		$this->cookie = (isset($GLOBALS['cookie']) ? $GLOBALS['cookie'] : null);
		$this->cart = (isset($GLOBALS['cart']) ? $GLOBALS['cart'] : null);
		$this->smarty = (isset($GLOBALS['smarty']) ? $GLOBALS['smarty'] : null);
		$this->link = (isset($GLOBALS['link']) ? $GLOBALS['link'] : null);

		$this->controller = new FroggyControllerBackwardModule();
		if (is_object($this->cookie))
		{
			$this->currency = new Currency((int)$this->cookie->id_currency);
			$this->language = new Language((int)$this->cookie->id_lang);
			$this->country = new Country((int)$this->cookie->id_country);
			$this->customer = new FroggyCustomerBackwardModule((int)$this->cookie->id_customer);
			$this->employee = new Employee((int)$this->cookie->id_employee);
		}
		else
		{
			$this->currency = null;
			$this->language = null;
			$this->country = null;
			$this->customer = null;
			$this->employee = null;
		}
		if (!Validate::isLoadedObject($this->currency))
			$this->currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));

		$this->shop = new FroggyShopBackwardModule();
	}


	/**
	 * Get a singleton context
	 *
	 * @return Context
	 */
	public static function getContext()
	{
		if (!isset(self::$instance))
			self::$instance = new FroggyContext();
		return self::$instance;
	}

	/**
	 * Clone current context
	 *
	 * @return Context
	 */
	public function cloneContext()
	{
		return clone($this);
	}

	/**
	 * @return int Shop context type (Shop::CONTEXT_ALL, etc.)
	 */
	public static function shop()
	{
		if (!self::$instance->shop->getContextType())
			return FroggyShopBackwardModule::CONTEXT_ALL;
		return self::$instance->shop->getContextType();
	}
}

/**
 * Class Shop for Backward compatibility
 */
class FroggyShopBackwardModule extends Shop
{
	const CONTEXT_ALL = 1;

	public $id = 1;
	public $id_shop_group = 1;
	public $physical_uri = __PS_BASE_URI__;


	public function getContextType()
	{
		return FroggyShopBackwardModule::CONTEXT_ALL;
	}

	public function setContext($var)
	{
		return true;
	}

	/**
	 * Simulate shop for 1.3 / 1.4
	 */
	public function getID()
	{
		return 1;
	}

	/**
	 * Get shop theme name
	 *
	 * @return string
	 */
	public function getTheme()
	{
		return _THEME_NAME_;
	}

	public function isFeatureActive()
	{
		return false;
	}
}

/**
 * Class Controller for a Backward compatibility
 * Allow to use method declared in 1.5
 */
class FroggyControllerBackwardModule
{
	/**
	 * @param $js_uri
	 * @return void
	 */
	public function addJS($js_uri)
	{
		Tools::addJS($js_uri);
	}

	/**
	 * @param $css_uri
	 * @param string $css_media_type
	 * @return void
	 */
	public function addCSS($css_uri, $css_media_type = 'all')
	{
		Tools::addCSS($css_uri, $css_media_type);
	}

	public function addJquery()
	{
		if (_PS_VERSION_ < '1.5')
			$this->addJS(_PS_JS_DIR_.'jquery/jquery-1.4.4.min.js');
		elseif (_PS_VERSION_ >= '1.5')
			$this->addJS(_PS_JS_DIR_.'jquery/jquery-1.7.2.min.js');
	}

}

/**
 * Class Customer for a Backward compatibility
 * Allow to use method declared in 1.5
 */
class FroggyCustomerBackwardModule extends Customer
{
	public $logged = false;
	/**
	 * Check customer informations and return customer validity
	 *
	 * @since 1.5.0
	 * @param boolean $with_guest
	 * @return boolean customer validity
	 */
	public function isLogged($with_guest = false)
	{
		if (!$with_guest && $this->is_guest == 1)
			return false;

		/* Customer is valid only if it can be load and if object password is the same as database one */
		if ($this->logged == 1 && $this->id && Validate::isUnsignedId($this->id) && Customer::checkPassword($this->id, $this->passwd))
			return true;
		return false;
	}
}
