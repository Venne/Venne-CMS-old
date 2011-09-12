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
 * @Entity
 * @Table(name="module")
 */
class ModuleEntity extends \Venne\Developer\Doctrine\BaseEntity {

	/** 
	 * @var string
	 * @Column(type="string")
	 */
	protected $moduleClass;
	
	/** 
	 * @var string
	 * @Column(type="string")
	 */
	protected $name;
	
	/** 
	 * @var string
	 * @Column(type="string")
	 */
	protected $version;
	
	/** 
	 * @var string
	 * @Column(type="string")
	 */
	protected $description;
	
	/**
	 * @var array of \Venne\ModuleManager\ClassEntity
	 * @OneToMany(targetEntity="classEntity", mappedBy="module", cascade={"persist", "remove", "detach"})
	 */
	protected $classes;


	public function __construct($moduleClass, $name, $description, $version)
	{
		$this->moduleClass = $moduleClass;
		$this->name = $name;
		$this->description = $description;
		$this->version = $version;
		$this->classes = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
	protected function register($type, $name, $className, $dependencies = array())
	{
		$dependencies = join(";", $dependencies);
		
		$entity = new ClassEntity($type, $this, $name, $className, $dependencies);
		$this->classes[] = $entity;
		return $entity;
	}
	
	protected function unregister($name)
	{
		unset($this->classes[$name]);
	}

	
	

	public function registerService($name, $className, $dependencies = array())
	{
		$this->register(ClassEntity::TYPE_SERVICE, $name, $className, $dependencies);
	}
	
	public function unregisterService($name)
	{
		$this->unregister($name);
	}
	
	public function registerRoute($name, $className,  $dependencies = array())
	{
		$entity = $this->register(ClassEntity::TYPE_ROUTE, $name, $className, $dependencies);
		$entity->addKey("routePrefix", $name);
	}
	
	public function unregisterRoute($name)
	{
		$this->unregister($name);
	}
		
}
