<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\ModulesModule;

use \Venne\Developer\Module\Service\IRouteService;

/**
 * @author Josef Kříž
 */
class Module extends \Venne\Developer\Module\AutoModule {

	public function getName()
	{
		return "modules";
	}
	
	public function getDescription()
	{
		return "Service for managing of modules";
	}

	public function getVersion()
	{
		return "0.1";
	}

	public function setServices(\Venne\Application\Container $container)
	{
		$container->services->addService("modules", function() use ($container) {
					return new Service($container, "modules");
				}
		);
		$container->services->addService("packages", function() use ($container) {
					return new PackagesService($container, "modules");
				}
		);
	}

}
