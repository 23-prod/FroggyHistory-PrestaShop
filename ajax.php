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

$configPath = '../../config/config.inc.php';
if (file_exists($configPath))
{
	include($configPath);
	$controller = new FrontController();
	$controller->init();

	if (file_exists(dirname(__FILE__).'/froggyhistory.php'))
	{
		include(dirname(__FILE__).'/froggyhistory.php');
		$fh = new FroggyHistory();
		if ($fh->ajax_secure_key != Tools::getValue('ajax_secure_key'))
			die('Invalid Secure Key');
		$fh->ajaxRequest();
		unset($fh);
	}
	else
		die('Class module wasn\'t found');
}
else
	die('Config file is missing');
