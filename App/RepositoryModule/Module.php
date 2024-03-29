<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\RepositoryModule;

use \Venne\Developer\Module\Service\IRouteService;

/**
 * @author Josef Kříž
 */
class Module extends \Venne\Developer\Module\AutoModule {


	public function getName()
	{
		return "repository";
	}


	public function getDescription()
	{
		return "Module for managing repositories";
	}


	public function getVersion()
	{
		return "0.1";
	}


	public function setServices(\Venne\Application\Container $container)
	{
		$container->services->addService("repository", function() use ($container) {
					return new Service($container, "repository");
				}
		);
	}
	
	public function setHooks(\Venne\Application\Container $container, \App\HookModule\Manager $manager)
	{
		$manager->addHook("admin\\menu", \callback($this, "hookAdminMenu"));
	}
	
	
	public function setRoutes(\Nette\Application\Routers\RouteList $router, $prefix = "")
	{
		$router[] = new \Nette\Application\Routers\Route($prefix . '[<repository>[/<package>]]', array(
					'module' => 'Repository',
					'presenter' => 'Default',
					'action' => 'default',
				)
		);
	}


	public function hookAdminMenu($menu)
	{
		$nav = new \App\NavigationModule\NavigationEntity("Repository");
		$nav->addKey("module", "Repository:Admin");
		$menu[] = $nav;
	}

}
