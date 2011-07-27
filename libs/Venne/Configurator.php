<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne;

use Nette,
	Nette\Caching\Cache,
	Nette\DI,
	Venne\CMS\Modules;

/**
 * @author     Josef Kříž
 */
class Configurator extends \Nette\Configurator {

	/**
	 * @param \Nette\DI\IContainer
	 * @return \Venne\Security\Authenticator
	 */
	public static function createServiceAuthenticator(\Nette\DI\IContainer $container)
	{
		return new Security\Authenticator($container);
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Venne\Security\Authorizator
	 */
	public static function createServiceAuthorizator(\Nette\DI\IContainer $container)
	{
		return new Security\Authorizator($container);
	}

	/**
	 * Loads configuration from file and process it.
	 * @return void
	 */
	public function loadConfig($file, $section = NULL)
	{
		parent::loadConfig($file, $section);
		$container = $this->getContainer();


		/*
		 * Load Modules
		 */
		foreach ($this->getContainer()->moduleManager->getModules() as $item) {
			$serviceClass = VENNE_MODULES_NAMESPACE . ucfirst($item) . "Service";

			$container->addService($item, function() use ($container, $serviceClass) {
						return new $serviceClass($container);
					});
		}
		
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Venne\CMS\ModuleManager
	 */
	public static function createServiceModuleManager(\Nette\DI\IContainer $container)
	{
		return new CMS\ModuleManager($container);
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Venne\CMS\Configurator
	 */
	public static function createServiceConfigurator(\Nette\DI\IContainer $container)
	{
		return new CMS\Configurator($container);
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Venne\CMS\RoutingManager
	 */
	public static function createServiceRouting(\Nette\DI\IContainer $container)
	{
		return new CMS\RoutingManager($container);
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Doctrine\ORM\EntityManager
	 */
	public static function createServiceEntityManager(\Nette\DI\IContainer $container)
	{

		//if ($container->getService("application")-> == "development") {
		$cache = new \Doctrine\Common\Cache\ArrayCache;
		//} else {
		//	$cache = new \Doctrine\Common\Cache\ApcCache();
		//}

		$config = new \Doctrine\ORM\Configuration();
		$config->setMetadataCacheImpl($cache);
		$driverImpl = $config->newDefaultAnnotationDriver(array(APP_DIR, VENNE_DIR));
		$config->setMetadataDriverImpl($driverImpl);
		$config->setQueryCacheImpl($cache);
		$config->setProxyDir(APP_DIR . '/proxies');
		$config->setProxyNamespace('App\Proxies');

		//if ($applicationMode == "development") {
		$config->setAutoGenerateProxyClasses(true);
		//} else {
		//	$config->setAutoGenerateProxyClasses(false);
		//}

		return \Doctrine\ORM\EntityManager::create((array) $container->params['database'], $config);
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Venne\Latte\DefaultMacros
	 */
	public static function createServiceMacros(\Nette\DI\IContainer $container)
	{
		return new \Venne\Latte\DefaultMacros;
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nella\Localization\ITranslator
	 */
	public static function createServiceTranslator(\Nette\DI\IContainer $container)
	{
		$translator = new \Nella\Localization\Translator();
		$translator->setLang($container->language->getCurrentLang($container->httpRequest)->name);
		$translator->addDictionary('Venne', WWW_DIR . "/templates/" . $container->params['CMS']["template"]);
		return $translator;
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nella\Localization\Panel
	 */
	public static function createServiceTranslatorPanel(\Nette\DI\IContainer $container)
	{
		return new \Nella\Localization\Panel($container);
	}

//	/**
//	 * @param \Nette\DI\IContainer
//	 * @return \Venne\CMS\Modules\NavigationService
//	 */
//	public static function createServiceNavigation(\Nette\DI\IContainer $container)
//	{
//		return new CMS\Models\NavigationService($container);
//	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Venne\CMS\Modules\WebsiteService
	 */
	public static function createServiceWebsite(\Nette\DI\IContainer $container)
	{
		return new CMS\Modules\WebsiteService($container);
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Venne\CMS\Modules\LanguageService
	 */
	public static function createServiceLanguage(\Nette\DI\IContainer $container)
	{
		return new CMS\Modules\LanguageService($container);
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nette\Database\Connection
	 */
	public static function createServiceDatabase(\Nette\DI\IContainer $container)
	{
		$driver = substr($container->params['database']['driver'], 4);
		$host = $container->params['database']['host'];
		$dbname = $container->params['database']['dbname'];
		$user = $container->params['database']['user'];
		$password = $container->params['database']['password'];

		return new Nette\Database\Connection("$driver:host=$host;dbname=$dbname", $user, $password);
	}

}