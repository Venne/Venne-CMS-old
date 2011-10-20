<?php

/**
 * Venne:CMS bootstrap file.
 */

// Load Nette Framework and Venne:CMS
$params['rootDir'] = __DIR__ . '/..';
$params['tempDir'] = $params['rootDir'] . '/temp';
$params['libsDir'] = $params['rootDir'] . '/libs';
$params['venneDir'] = $params['libsDir'] . '/Venne';
require $params['venneDir'] . '/loader.php';


// Configure and run the application!
$application->run();
