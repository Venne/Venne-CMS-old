<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS;

use Venne,
	Nette\Application\Routers\SimpleRouter,
	Nette\Application\Routers\Route;

/**
 * Description of RoutingManager
 *
 * @author Josef Kříž
 */
class RoutingManager{
	
	/** @var Nette\DI\Container */
	protected $container;
	
	public function __construct($container)
	{
		$this->container = $container;
	}


	/**
	 * @return array of Nette\Application\Route
	 */
	public function setFrontRoutes($router){
				
		$prefix = $this->container->website->current->routePrefix;
		
		/*
		 * Default route values
		 */
		$values = array(
					\Nette\Application\Routers\Route::FILTER_IN => callback($this->container->language, "getLanguageIdByAlias"),
					\Nette\Application\Routers\Route::FILTER_OUT => callback($this->container->language, "getLanguageAliasById"),
			);
		/* Hide default lang */
		if($this->container->website->current->langType == Modules\Website::LANG_IN_GET){
			$values += array(
				\Nette\Application\Routers\Route::VALUE => $this->container->website->current->langDefault,
			);
		}
		$values = array("lang"=>$values);
		
		/*
		 * Default route
		 */
		$router[] = new Route('', $this->container->params["venne"]["defaultModule"].":Default:", Route::ONE_WAY);
		$router[] = new Route($prefix, $this->container->params["venne"]["defaultModule"].":Default:", Route::ONE_WAY);
		
		/*
		 * Routes for modules
		 */
		foreach($this->container->moduleManager->getRouteModules() as $module){
			$this->container->{$module}->getRoute($router, $values, $prefix . $this->container->params["venne"]["modules"][$module."Module"]["routePrefix"]);
		}

	}
	
	/**
	 * @return array of Nette\Application\Route
	 */
	public function setAdminRoutes($router, $prefix = ""){
		
		$router[] = new Route('admin/index.php', 'Homepage:default', Route::ONE_WAY);
		$router[] = new Route('admin/scripts/<module>/<presenter>', 'Scripts:Thumb');
				
		
		$router[] = new Route('admin/<module>/<presenter>/<action>[/<id>]?lang=<lang>&langEdit=<langEdit>&webId=<webId>', array(
				'module' => 'Default',
				'presenter' => 'Default',
				'action' => 'default',
				'webId' => NULL,
				'lang' => array(
					Route::FILTER_IN => callback($this->container->language, "getLanguageIdByAlias"),
					Route::FILTER_OUT => callback($this->container->language, "getLanguageAliasById"),
				),
				'langEdit' => array(
					Route::FILTER_IN => callback($this->container->language, "getLanguageIdByAlias"),
					Route::FILTER_OUT => callback($this->container->language, "getLanguageAliasById"),
				)
			)
		);
	}
	
	/**
	 * @return array of Nette\Application\Route
	 */
	public function setInstallationRoutes($router, $prefix = ""){
		
		$router[] = new Route('admin/index.php', 'Homepage:default', Route::ONE_WAY);
		$router[] = new Route('admin/scripts/<module>/<presenter>', 'Scripts:Thumb');
		
		
		$router[] = new Route('admin/<module>/<presenter>/<action>[/<id>]', array(
				'module' => 'Default',
				'presenter' => 'Default',
				'action' => 'default'
			)
		);
		
	}
	
}
