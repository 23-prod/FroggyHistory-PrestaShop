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

class FroggyModule extends Module
{

	/**
	 * @var mixed
	 */
	protected $definitions_elements = array('hooks', 'configurations', 'controllers', 'sql');

	/**
	 * Construct for module
	 */
	public function __construct()
	{
		// Set name with ClassName of module
		$this->name = strtolower(get_class($this));

		// Get definition file content
		$parser = new FroggyDefinitionsModuleParser(_PS_MODULE_DIR_.$this->name.'/definitions.json');
		$definitions = $parser->parse();

		$this->tab = isset($definitions['tab']) ? $definitions['tab'] : null;
		$this->version = isset($definitions['version']) ? $definitions['version'] : 1;
		$this->need_instance = isset($definitions['need_instance']) ? $definitions['need_instance'] : 0;

		$this->author = 'Froggy Commerce';

		parent::__construct();

		foreach ($this->definitions_elements as $key) {
			if (isset($definitions[$key])) {
				$this->$key = $definitions[$key];
			}
		}
	}

	/**
	 * Method for module installation
	 *
	 * @return bool
	 */
	public function install()
	{
		if (parent::install()) {
			if (!$this->registerDefinitionsHooks()) {
				return false;
			}

			if (!$this->registerDefinitionsConfigurations()) {
				return false;
			}

			if (!$this->registerDefinitionsControllers()) {
				return false;
			}

			if (!$this->runDefinitionsSql()) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Method for module uninstallation
	 *
	 * @return bool
	 */
	public function uninstall()
	{
		if (parent::uninstall()) {
			if (!$this->deleteConfigurations()) return false;
			if (!$this->deleteModuleControllers()) return false;
			if (!$this->runDefinitionsSql('uninstall')) return false;
		}
		return false;
	}

	/**
	 * Enable module (and tabs = admin controllers access)
	 *
	 * @param bool $force_all Force enable
	 * @return bool Result of enabling
	 */
	public function enable($force_all = false)
	{
		Tab::enablingForModule($this->name);
		return parent::enable($force_all);
	}

	/**
	 * Disable module (and tabs = admin controllers access)
	 *
	 * @param bool $force_all Force enable
	 * @return bool Result of disabling
	 */
	public function disable($force_all = false)
	{
		Tab::disablingForModule($this->name);
		return parent::disable($force_all);
	}

	/**
	 * Register hooks of module
	 *
	 * @return bool
	 */
	protected function registerDefinitionsHooks()
	{
		if (isset($this->hooks) && is_array($this->hooks)) {
			foreach ($this->hooks as $hook) {
				if (!$this->registerHook($hook)) return false;
			}
		}
		return true;
	}

	/**
	 * Register configuration for module
	 *
	 * @return bool
	 */
	protected function registerDefinitionsConfigurations()
	{
		if (isset($this->configurations) && is_array($this->configurations)) {
			foreach ($this->configurations as $name => $value) {
				// In multilanguage case
				if (is_array($value)) {
					$values = array();
					foreach (Language::getLanguages(false) as $language) {
						if (isset($value[$language['iso_code']])) {
							$values[$language['id_lang']] = $value[$language['iso_code']];
						}
						else {
							$values[$language['id_lang']] = $value['default'];
						}
					}
					$value = $values;
				}

				if (!Configuration::updateValue($name, $value)) return false;
			}
		}
		return true;
	}

	/**
	 * Delete configurations of module
	 *
	 * @return bool
	 */
	protected function deleteConfigurations()
	{
		if (isset($this->configurations) && is_array($this->configurations)) {
			foreach ($this->configurations as $name => $value) {
				if (!Configuration::deleteByName($name)) return false;
			}
		}
		return true;
	}

	/**
	 * Install module controllers
	 *
	 * @return bool
	 */
	protected function registerDefinitionsControllers()
	{
		if (isset($this->controllers) && is_array($this->controllers)) {
			foreach ($this->controllers as $controller) {
				$tab = new Tab();
				$tab->class_name = $controller['classname'];
				$tab->module = $this->name;
				$tab->id_parent = Tab::getIdFromClassName($controller['parent']);

				foreach (Language::getLanguages(false) as $language) {
					if (isset($data['name'][$language['iso_code']])) {
						$tab->name[$language['id_lang']] = $controller['name'][$language['iso_code']];
					}
					else {
						$tab->name[$language['id_lang']] = $controller['name']['default'];
					}
				}

				if (!$tab->add()) return false;
			}
		}
		return true;
	}

	/**
	 * Uninstall module controllers
	 *
	 * @return bool
	 */
	protected function deleteModuleControllers()
	{
		if (isset($this->controllers) && is_array($this->controllers)) {
			foreach ($this->controllers as $controller) {
				$id_tab = Tab::getIdFromClassName($controller['classname']);
				$tab = new Tab($id_tab);
				if (!$tab->delete()) return false;
			}
		}
		return true;
	}

	/**
	 * Run SQL file
	 *
	 * @param string $type
	 * @return bool
	 * @throws Exception
	 */
	protected function runDefinitionsSql($type = 'install')
	{
		if (isset($this->sql) && is_array($this->sql)) {
			if (!isset($this->sql[$type])) {
				throw new Exception('SQL file type not exists');
			}

			foreach ($this->sql[$type] as $file) {
				if (!file_exists(__DIR__.'/'.$file)) {
					throw new Exception('This SQL file not exists');
				}

				$content = file_get_contents($file);
				$content = str_replace('@PREFIX@', _DB_PREFIX_, $content);
				$content = str_replace('@ENGINE@', _MYSQL_ENGINE_, $content);
				$queries = preg_split("/;\s*[\r\n]+/", $content);

				foreach($queries as $query) {
					if (!empty($query)) {
						if (!Db::getInstance()->execute(trim($query))) return false;
					}
				}
			}
		}
		return true;
	}

	/**
	 * Get all module configurations keys
	 * @return array
	 */
	public function getModuleConfigurationsKeys()
	{
		return array_keys($this->configurations);
	}

	/**
	 * Get all module configurations values
	 * @return mixed
	 */
	public function getModuleConfigurations()
	{
		return Configuration::getMultiple($this->getModuleConfigurationsKeys());
	}

}

class FroggyDefinitionsModuleParser
{

	/**
	 * @var
	 */
	protected $filepath;

	/**
	 * @param $filepath
	 */
	public function __construct($filepath)
	{
		if (!file_exists($filepath)) {
			throw new Exception('File given to definitions parser does not exists');
		}
		$this->filepath = $filepath;
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	public function parse()
	{
		$definitions = json_decode(file_get_contents($this->filepath), true);
		if (is_null($definitions)) {
			throw new Exception('Definition parser cannot decode file');
		}

		return $definitions;
	}

}
