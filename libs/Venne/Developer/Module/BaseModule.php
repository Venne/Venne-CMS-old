<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Module;

/**
 * @author Josef Kříž
 */
abstract class BaseModule implements IModule {

	/**
	 * @param array of Nette\Application\Routers\RouteList
	 */
	public function setRoutes(\Nette\Application\Routers\RouteList $router, $prefix = "")
	{
		
	}
	
	public function setPermissions(\Venne\Application\Container $container, \Venne\Security\Authorizator $permissions)
	{
		
	}
	
	public function setListeners(\Venne\Application\Container $container)
	{
		
	}
	
	public function setServices(\Venne\Application\Container $container)
	{
		
	}

	public function setHooks(\Venne\Application\Container $container, \Venne\HookModule\Manager $manager)
	{
		
	}
	
	public function install()
	{
		
	}
	
	public function uninstall()
	{
		
	}
	
}

