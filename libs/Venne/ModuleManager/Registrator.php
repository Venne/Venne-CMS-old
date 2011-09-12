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
class Registrator {

	/** @var */
	protected $entityManager;
	
	/** @var string */
	protected $name;
	
	public function __construct($entityManager, $name)
	{
		$this->entityManager = $entityManager;
		$this->name = $this->name;
	}
	
	public function registerService($name, $class, $dependencies = array())
	{
		
	}
	
	public function unregisterService($name)
	{
		
	}
	
	public function registerMacro()
	{
		
	}
	
	public function registerRoute()
	{
		
	}
	
	
}
