<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\SecurityModule;

use \Venne\Developer\Module\Service\IRouteService;

/**
 * @author Josef Kříž
 */
class Module extends \Venne\Developer\Module\AutoModule {

	public function getName()
	{
		return "security";
	}


	public function getDescription()
	{
		return "Module for managing security";
	}


	public function getVersion()
	{
		return "0.1";
	}


	public function setServices(\Venne\Application\Container $container)
	{
		$container->services->addService("user", function() use ($container) {
					return new UserService($container, "user", $container->doctrineContainer->entityManager);
				}
		);
		$container->services->addService("role", function() use ($container) {
					return new RoleService($container, "role", $container->doctrineContainer->entityManager);
				}
		);
		$container->services->addService("permission", function() use ($container) {
					return new RoleService($container, "permission", $container->doctrineContainer->entityManager);
				}
		);
	}

}
