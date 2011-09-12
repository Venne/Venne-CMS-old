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
	Nette\Diagnostics\Debugger,
	Venne\Modules,
	Nette\Application\Routers\SimpleRouter,
	Nette\Application\Routers\Route;

/**
 * @author     Josef Kříž
 * 
 * @property-read \Nette\Application\Container $container
 */
class Configurator extends \Nette\Configurator {

	/** @var array */
	protected $defaultModules = array(
			"hook" => array(),
			"website" => array(),
			"system" => array(),
			"security" => array(),
			"navigation" => array(),
			"comments" => array(),
			"modules" => array(),
			"error" => array(),
			"layout" => array(),
		);
	
	public function __construct($params, $containerClass = 'Venne\Application\Container')
	{
		require_once $params["libsDir"] . '/Venne/DI/Container.php';
		require_once $params["libsDir"] . '/Venne/Application/Container.php';
		parent::__construct($containerClass);

		$this->container->addService("services", new \Venne\DI\Container($this->container));
		$this->container->addService("modules", new \Venne\DI\Container($this->container));
		$this->container->addService("routes", new \Venne\DI\Container($this->container));
		$this->container->addService("pages", new \Venne\DI\Container($this->container));
		
		/*
		 * Params
		 */
		$this->container->params += (array) $params;
		$this->container->params["rootDir"] = $this->container->params["wwwDir"] . '/..';
		$this->container->params["extensionsDir"] = $this->container->params["rootDir"] . '/extensions';
		$this->container->params['flashes'] = array(
			'success' => "success",
			'error' => "error",
			'info' => "info",
			'warning' => "warning",
		);
		$this->container->params["venneDir"] = $this->container->params["libsDir"] . '/Venne';
		$this->container->params["frontDir"] = $this->container->params["rootDir"] . '/app';
		$this->container->params["flagsDir"] = $this->container->params["rootDir"] . '/flags';
		$this->container->params["configsDir"] = $this->container->params["appDir"] . '/configs';

		$this->container->params["venneModeInstallation"] = false;
		$this->container->params["venneModeAdmin"] = false;
		$this->container->params["venneModeFront"] = false;

		$this->container->params["venneModulesNamespace"] = "\\Venne\\Modules\\";
		
		/*
		 * Detect mode
		 */
		$url = explode("/", substr($this->container->httpRequest->url->path, strlen($this->container->httpRequest->url->basePath)), 2);
		if($url[0] == "admin"){
			$this->container->params["venneModeAdmin"] = true;
		}else if($url[0] == "installation"){
			$this->container->params["venneModeInstallation"] = true;
		}else{
			$this->container->params["venneModeFront"] = true;
		}
		
		
		/*
		 * Set mode
		 */
		$config = \Nette\Config\NeonAdapter::load($this->container->params["appDir"] . "/config.neon");
		if ($config["global"]["mode"] == "production") {
			$this->container->params['productionMode'] = true;
		} else if ($config["global"]["mode"] == "development") {
			$this->container->params['productionMode'] = false;
		} else {
			if ($this->container->params['productionMode']) {
				if (count($config["global"]["developerIp"]) > 0) {
					$remoteIp = $_SERVER['REMOTE_ADDR'];
					foreach ($config["global"]["developerIp"] as $ip) {
						if ($ip == $remoteIp) {
							$this->container->params['productionMode'] = false;
							break;
						}
					}
				}
			}
		}
		Debugger::$strictMode = TRUE;
		Debugger::enable($this->container->params['productionMode']);
	}


	/**
	 * Loads configuration from file and process it.
	 * @return DI\Container
	 */
	public function loadConfig($file, $section = NULL)
	{
		$container = parent::loadConfig($file, $section);
		$this->container->params["venne"]["moduleNamespaces"] = array("\\Venne\\", "\\");
		$this->container->params['venne']['modules'] = $this->defaultModules + $this->container->params['venne']['modules'];
		
		foreach($this->container->params['venne']['modules'] as $key=>$module){
			$class = ucfirst($key) . "Module\\Module";
			foreach($this->container->params["venne"]["moduleNamespaces"] as $ns){
				if(class_exists($ns . $class)){
					$class = $ns . $class;
					break;
				}
			}
			$this->container->modules->addService($key, new $class);
			$this->container->modules->$key->setListeners($this->container);
		}
		foreach($this->container->params['venne']['modules'] as $key=>$module){
			$this->container->modules->$key->setServices($this->container);
		}
		foreach($this->container->params['venne']['modules'] as $key=>$module){
			$this->container->modules->$key->setHooks($this->container, $this->container->hookManager);
		}
		
		$this->setRoutes($container->application->router);
		
		return $container;
	}

	/**
	 * @param \Nette\Application\Routers\RouteList
	 */
	public function setRoutes(\Nette\Application\Routers\RouteList $router)
	{
		$prefix = $this->container->services->website->current->routePrefix;


		$router[] = $adminRouter = new \Venne\Application\Routers\RouteList("admin");
		$adminRouter[] = new Route('admin/<module>/<presenter>[/<action>[/<id>]]', array(
					'module' => "Default",
					'presenter' => 'Default',
					'action' => 'default',
				));
		
		$router[] = $installationRouter = new \Venne\Application\Routers\RouteList("admin");
		$installationRouter[] = new Application\Routers\InstallationRoute('installation/<presenter>[/<action>[/<id>]]', array(
					'presenter' => 'Default',
					'action' => 'default',
				));

		/*
		 * Routes for modules
		 */
		foreach ($this->container->params["venne"]["modules"] as $key => $module) {
			if(isset($module["routePrefix"])){
				$this->container->modules->$key->setRoutes($router, $prefix . $module["routePrefix"]);
			}
		}

		/*
		 * Default route
		 */
		$router[] = new Route('', $this->container->params["venne"]["website"]["defaultModule"] . ":Default:", Route::ONE_WAY);
		if ($prefix) {
			$router[] = new Route($prefix, $this->container->params["venne"]["website"]["defaultModule"] . ":Default:", Route::ONE_WAY);
		}
	}

	/**
	 * @param \Nette\DI\IContainer
	 * @return \Venne\Doctrine\Container
	 */
	public static function createServiceDoctrineContainer(\Nette\DI\IContainer $container)
	{
		return new Doctrine\Container($container);
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Venne\Doctrine\Container
	 */
	public static function createServiceModuleManager(\Nette\DI\IContainer $container)
	{
		return new ModuleManager\Manager($container, $container->doctrineContainer->entityManager);
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nette\Database\Connection
	 */
	public static function createServiceDatabaseService(\Nette\DI\IContainer $container)
	{
		$driver = substr($container->params['database']['driver'], 4);
		$host = $container->params['database']['host'];
		$dbname = $container->params['database']['dbname'];
		$user = $container->params['database']['user'];
		$password = $container->params['database']['password'];

		return new Nette\Database\Connection("$driver:host=$host;dbname=$dbname", $user, $password);
	}


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
	 * @param \Nette\DI\IContainer
	 * @return \Venne\RoutingManager
	 */
	public static function createServiceRouting(\Nette\DI\IContainer $container)
	{
		return new RoutingManager\Service($container);
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
		$translator->setLang($container->cms->language->getCurrentLang($container->httpRequest)->name);
		$translator->addDictionary('Venne', $container->params["wwwDir"] . "/templates/" . $container->params['CMS']["template"]);
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


	/**
	 * @return Nette\Loaders\RobotLoader
	 */
	public static function createServiceRobotLoader(DI\Container $container, array $options = NULL)
	{
		$loader = new Nette\Loaders\RobotLoader;
		$loader->autoRebuild = isset($options['autoRebuild']) ? $options['autoRebuild'] : !$container->params['productionMode'];
		$loader->setCacheStorage($container->cacheStorage);
		if (isset($options['directory'])) {
			$loader->addDirectory($options['directory']);
		} else {
			foreach (array('appDir', 'libsDir', 'extensionsDir') as $var) {
				if (isset($container->params[$var])) {
					$loader->addDirectory($container->params[$var]);
				}
			}
		}
		$loader->register();
		return $loader;
	}

}
