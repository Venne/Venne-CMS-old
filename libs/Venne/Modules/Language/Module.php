<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\LanguageModule;

use \Venne\Developer\Module\Service\IRouteService;

/**
 * @author Josef Kříž
 */
class Module extends \Venne\Developer\Module\BaseModule {

	public function getName()
	{
		return "language";
	}
	
	public function getDescription()
	{
		return "Service for managing of languages";
	}

	public function getVersion()
	{
		return "0.1";
	}

	public function setPermissions(\Venne\Security\Authorizator $permissions)
	{
		$permissions->addResource("administration-websites", "administration");
		$permissions->addResource("administration-websites-edit", "administration-websites");
		$permissions->addPrivilege("pages", array("create", "edit"));
	}


	public function setServices(\Venne\Application\Container $container)
	{
		$container->services->addService("language", function() use ($container) {
					return new Service($container, "language", $container->doctrineContainer->entityManager);
				}
		);
	}

}
