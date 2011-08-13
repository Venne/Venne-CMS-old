<?php

/**
 * My Application bootstrap file.
 */


use Nette\Diagnostics\Debugger,
	Nette\Application\Routers\SimpleRouter,
	Nette\Application\Routers\Route;


// Load Nette Framework
$params['libsDir'] = __DIR__ . '/../libs';
require $params['libsDir'] . '/Venne/loader.php';


// Load configuration from config.neon file
$configurator = new Venne\Configurator($params);
//$configurator->container->params += $params;
$configurator->container->params['tempDir'] = __DIR__ . '/../temp';
$container = $configurator->loadConfig(__DIR__ . '/../config.neon');


// Setup router
$routingManager = $container->routing;
$routingManager->setRoutes($container->application->router);


// Configure and run the application!
$application = $container->application;
$application->catchExceptions = (bool) Debugger::$productionMode;
$application->errorPresenter = ucfirst($container->params['venne']['defaultErrorModule']).":Error";
$application->run();
