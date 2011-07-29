<?php

/**
 * My Application bootstrap file.
 */


use Nette\Diagnostics\Debugger,
	Nette\Application\Routers\SimpleRouter,
	Nette\Application\Routers\Route;


// Load Nette Framework
// this allows load Nette Framework classes automatically so that
// you don't have to litter your code with 'require' statements
require LIBS_DIR . '/Venne/loader.php';


// Enable Nette\Debug for error visualisation & logging
Debugger::$strictMode = TRUE;
Debugger::enable();


// Load configuration from config.neon file
$configurator = new Venne\Configurator;
$configurator->loadConfig(__DIR__ . '/../config.neon', "common");


// Configure application
$application = $configurator->container->application;
$application->errorPresenter = 'Error';
$application->catchExceptions = (bool) Debugger::$productionMode;


// Setup router
$routingManager = $configurator->container->routing;
$application->onStartup[] = function() use ($application, $routingManager) {
	$routingManager->setFrontRoutes($application->getRouter());
};


// Run the application!
$application->run();
