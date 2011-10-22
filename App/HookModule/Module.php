<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\HookModule;

use \Venne\Developer\Module\Service\IRouteService;

/**
 * @author Josef Kříž
 */
class Module extends \Venne\Developer\Module\BaseModule {

	public function getName()
	{
		return "hook";
	}
	
	public function getDescription()
	{
		return "hook";
	}

	public function getVersion()
	{
		return "0.1";
	}

	public function setServices(\Venne\Application\Container $container)
	{
		$container->addService("hookManager", function() use ($container) {
					return new Manager($container, "modules");
				}
		);
	}

}
