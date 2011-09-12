<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\NavigationModule;

use \Venne\Developer\Module\Service\IRouteService;

/**
 * @author Josef Kříž
 */
class Module extends \Venne\Developer\Module\AutoModule {


	public function getName()
	{
		return "navigation";
	}


	public function getDescription()
	{
		return "Module for managing navigation";
	}


	public function getVersion()
	{
		return "0.1";
	}


	public function setServices(\Venne\Application\Container $container)
	{
		$container->services->addService("navigation", function() use ($container) {
					return new Service($container, "navigation", $container->doctrineContainer->entityManager);
				}
		);

		$container->addService("navigationListener", function() use ($container) {
					return new NavigationListener($container->cacheStorage);
				}, array("listener")
		);
	}


	public function setHooks(\Venne\Application\Container $container, \Venne\HookModule\Manager $manager)
	{
		$manager->addHook("admin\\menu", \callback($container->services->navigation, "hookAdminMenu"));
		$manager->addHookExtension(\Venne\HookModule\Manager::EXTENSION_CONTENT, new \Venne\Modules\NavigationContentExtension($container->services->navigation));
	}

}
