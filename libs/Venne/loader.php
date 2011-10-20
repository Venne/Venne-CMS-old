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

/**
 * Load Nette framework
 */
require_once $params['libsDir'] . "/Nette/loader.php";
require_once __DIR__  . '/DI/Container.php';
require_once __DIR__  . '/Application/Container.php';
require_once __DIR__ . "/Configurator.php";

/**
 * Load and configure Nette Framework.
 */
define('VENNE', TRUE);
define('VENNE_DIR', __DIR__);
define('VENNE_VERSION_ID', '2.0000');
define('VENNE_VERSION_STATE', 'pre-alpha');

$configurator = new Venne\Configurator($params);
$container = $configurator->loadConfig(__DIR__ . '/config.neon');
$application = $container->application;
$application->catchExceptions = (bool) Debugger::$productionMode;
$application->errorPresenter = ucfirst($container->params['website']['defaultErrorModule']) . ":Error";
