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
	public static $defaultModules = array(
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
		require_once $params['venneDir'] . '/DI/Container.php';
		require_once $params['venneDir'] . '/Application/Container.php';
		parent::__construct($containerClass);

		$this->container->addService("services", new \Venne\DI\Container($this->container));
		$this->container->addService("modules", new \Venne\DI\Container($this->container));
		$this->container->addService("routes", new \Venne\DI\Container($this->container));
		$this->container->addService("pages", new \Venne\DI\Container($this->container));
		$this->container->addService("themes", new \Venne\DI\Container($this->container));

		/*
		 * Params
		 */
		$this->container->params += (array) $params;
		$this->container->params['flashes'] = array(
			'success' => "success",
			'error' => "error",
			'info' => "info",
			'warning' => "warning",
		);

		$this->container->params["venneModeInstallation"] = false;
		$this->container->params["venneModeAdmin"] = false;
		$this->container->params["venneModeFront"] = false;
		$this->container->params["venneModulesNamespace"] = "\\Venne\\Modules\\";

		/*
		 * Detect mode
		 */
		$url = explode("/", substr($this->container->httpRequest->url->path, strlen($this->container->httpRequest->url->basePath)), 2);
		if ($url[0] == "admin") {
			$this->container->params["venneModeAdmin"] = true;
		} else if ($url[0] == "installation") {
			$this->container->params["venneModeInstallation"] = true;
		} else {
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
		$this->container->params["moduleNamespaces"] = array("\\App\\", "\\");
		$this->container->params['modules'] = self::$defaultModules + $this->container->params['modules'];

		foreach ($this->container->params['modules'] as $key => $module) {
			$class = ucfirst($key) . "Module\\Module";
			foreach ($this->container->params["moduleNamespaces"] as $ns) {
				if (class_exists($ns . $class)) {
					$class = $ns . $class;
					break;
				}
			}
			$this->container->modules->addService($key, new $class);
			$this->container->modules->$key->setListeners($this->container);
		}
		foreach ($this->container->params['modules'] as $key => $module) {
			$this->container->modules->$key->setServices($this->container);
		}
		foreach ($this->container->params['modules'] as $key => $module) {
			$this->container->modules->$key->setHooks($this->container, $this->container->hookManager);
		}

		$this->setRoutes($container->application->router);


		// load themes
		foreach ($this->container->services->modules->getThemes() as $skin) {
			$class = "\\" . ucfirst($skin) . "Theme\\Theme";
			$this->container->themes->addService($skin, new $class($container));
		}

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

		/*
		 * Routes for modules
		 */
		foreach ($this->container->params["modules"] as $key => $module) {
			if (isset($module["routePrefix"])) {
				$this->container->modules->$key->setRoutes($router, $prefix . $module["routePrefix"]);
			}
		}

		/*
		 * Default route
		 */
		$router[] = new Route('', $this->container->params["website"]["defaultPresenter"] . ":", Route::ONE_WAY);
		if ($prefix) {
			$router[] = new Route($prefix, $this->container->params["website"]["defaultPresenter"] . ":", Route::ONE_WAY);
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
		return new \App\SecurityModule\Authenticator($container);
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \App\SecurityModule\Authorizator
	 */
	public static function createServiceAuthorizator(\Nette\DI\IContainer $container)
	{
		return new \App\SecurityModule\Authorizator($container);
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
		$translator = new \Venne\Localization\Translator();
		$translator->setLang("cs");

		$file = $container->params["wwwDir"] . "/themes/" . $container->params["website"]["theme"];
		$translator->addDictionary('Venne', $file);

		foreach ($container->params['modules'] as $key => $module) {
			$file = $container->params["appDir"] . "/" . ucfirst($key) . "Module";
			if (file_exists($file)) {
				$translator->addDictionary($key, $file);
			}
		}

		if ($container->params["venneModeAdmin"]) {
			$file = $container->params["wwwDir"] . "/themes/admin";
			$translator->addDictionary('Administration', $file);
		}

		return $translator;
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nella\Localization\Panel
	 */
	public static function createServiceTranslatorPanel(\Nette\DI\IContainer $container)
	{
		return new \Venne\Localization\Panel($container);
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nette\Latte\Engine
	 */
	public static function createServiceLatteEngine(\Nette\DI\IContainer $container)
	{
		$engine = new Nette\Latte\Engine();

		/*
		 * Load macros
		 */
		foreach ($container->params["macros"] as $item) {
			$class = "\Venne\Latte\Macros\\" . ucfirst($item) . "Macro";
			$class::install($engine->parser);
		}

		return $engine;
	}


	/**
	 * @param \Nette\DI\IContainer $container
	 * @return Templating\TemplateContainer 
	 */
	public static function createServiceTemplateContainer(\Nette\DI\IContainer $container)
	{
		return new Templating\TemplateContainer($container->latteEngine, $container->translator);
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
			foreach (array('appDir', 'libsDir', 'themesDir') as $var) {
				if (isset($container->params[$var])) {
					$loader->addDirectory($container->params[$var]);
				}
			}
		}
		$loader->register();
		return $loader;
	}


	/**
	 * @return \Venne\Application\IPresenterFactory
	 */
	public static function createServicePresenterFactory(DI\Container $container)
	{
		return new \Venne\Application\PresenterFactory($container);
	}

}
