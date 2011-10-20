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

use Nette\Diagnostics\Debugger,
	Nette\Application\Routers\SimpleRouter,
	Nette\Application\Routers\Route;

/**
 * Load Nette framework
 */
require_once $params['libsDir'] . "/Nette/loader.php";
require_once $params['venneDir'] . '/DI/Container.php';
require_once $params['venneDir'] . '/Application/Container.php';
require_once $params['venneDir'] . "/Configurator.php";

/**
 * Load and configure Nette Framework.
 */
define('VENNE', TRUE);
define('VENNE_DIR', __DIR__);
define('VENNE_VERSION_ID', '2.0000');
define('VENNE_VERSION_STATE', 'pre-alpha');

$configurator = new Configurator($params);
$container = $configurator->loadConfig($params['appDir'] . '/config.neon');
$application = $container->application;
$application->catchExceptions = (bool) Debugger::$productionMode;
$application->errorPresenter = ucfirst($container->params['website']['defaultErrorModule']) . ":Error";
