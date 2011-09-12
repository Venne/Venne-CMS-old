<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\LayoutModule;

use \Venne\Developer\Module\Service\IRouteService;

/**
 * @author Josef Kříž
 */
class Module extends \Venne\Developer\Module\AutoModule {


	public function getName()
	{
		return "layout";
	}


	public function getDescription()
	{
		return "Module for managing layouts";
	}


	public function getVersion()
	{
		return "0.1";
	}


	public function setServices(\Venne\Application\Container $container)
	{
		$container->services->addService("layout", function() use ($container) {
					return new \Venne\LayoutModule\Service($container, "layout", $container->doctrineContainer->entityManager);
				}
		);
	}
	
	public function setHooks(\Venne\Application\Container $container, \Venne\HookModule\Manager $manager)
	{
		$manager->addHook("admin\\menu", \callback($container->services->layout, "hookAdminMenu"));
		$manager->addHookExtension(\Venne\HookModule\Manager::EXTENSION_CONTENT, new LayoutContentExtension($container->services->layout));
	}

}
