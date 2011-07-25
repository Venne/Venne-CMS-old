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
require_once __DIR__ . "/../Nette/loader.php";


require_once __DIR__ . "/Configurator.php";

/**
 * Load and configure Nette Framework.
 */
define('VENNE', TRUE);
define('VENNE_DIR', __DIR__);
define('VENNE_VERSION_ID', '2.0000');
define('VENNE_VERSION_STATE', 'pre-alpha'); 

define('VENNE_MODULES_NAMESPACE', "\\Venne\\CMS\Modules\\");
