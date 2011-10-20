<?php

/**
 * Venne:CMS bootstrap file.
 */
use Nette\Diagnostics\Debugger,
	Nette\Application\Routers\SimpleRouter,
	Nette\Application\Routers\Route;

// Load Nette Framework and Venne:CMS
$params['rootDir'] = __DIR__ . '/..';
$params['libsDir'] = $params['rootDir'] . '/libs';
require $params['libsDir'] . '/Venne/loader.php';


// Configure and run the application!
$application->run();
