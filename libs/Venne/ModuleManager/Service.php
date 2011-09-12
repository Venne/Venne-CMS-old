<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\ModuleManager;

use Venne,
	Nette\Application\Routers\SimpleRouter,
	Nette\Application\Routers\Route;

/**
 * @author Josef Kříž
 */
class Service extends Venne\Developer\Service\DoctrineService {


	public $entityNamespace = "\\Venne\\ModuleManager\\";
	
}
