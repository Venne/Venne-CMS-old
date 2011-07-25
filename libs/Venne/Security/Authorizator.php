<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Security;

use Venne;

/**
 * @author Josef Kříž
 */
class Authorizator extends \Nette\Security\Permission {


	/** @var \Nette\DI\Container */
	private $container;
	public $defaultResources = array();
	public $defaultRoles = array();


	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(\Nette\DI\Container $container)
	{
		$this->container = $container;
		
		/*
		 * Update identity
		 */
		$identity = $this->container->user->getIdentity();
		if($identity instanceof \Venne\CMS\Modules\User){
			$identity2 = $this->container->users->getRepository()->find($identity->id);
			$identity->setRoles($identity2->getRoles());
			$identity->setData($identity2->getData());
		}
		
		
//		$cache = new \Nette\Caching\Cache($container->cacheStorage, "Venne");
//		if(($resources = $cache->load("authorizator-resources")) == NULL){
//			$resources = $this->container->entityManager->getRepository("\\Venne\\CMS\\Modules\\Resource")->findAll();
//			$cache->save("authorizator-resources", $resources);
//		}
//		
//		if(($roles = $cache->load("authorizator-roles")) == NULL){
//			$roles = $this->container->entityManager->getRepository("\\Venne\\CMS\\Modules\\Role")->findAll();
//			$cache->save("authorizator-roles", $roles);
//		}
		
		$resources = $this->container->entityManager->getRepository("\\Venne\\CMS\\Modules\\Resource")->findAll();
		$roles = $this->container->entityManager->getRepository("\\Venne\\CMS\\Modules\\Role")->findAll();


		/*
		 * Add resources
		 */
		foreach ($this->defaultResources as $resource) {
			$this->addResource($resource);
		}
		foreach ($resources as $resource) {
			$this->addResource($resource->name);
		}

		/*
		 * Add roles
		 */
		foreach ($this->defaultRoles as $role) {
			$this->addRole($role);
		}
		foreach ($roles as $role) {
			$this->addRole($role->name, $role->parent ? $role->parent->name : NULL);

			foreach ($role->permissions as $permission) {
				if ($permission->allow) {
					$this->allowWithRecursion($permission->resource, $role->name, $permission->resource->name, $permission->privilege ? $permission->privilege : NULL);
				} else {
					$this->deny($permission->resource, $role->name, $permission->resource->name, $permission->privilege ? $permission->privilege : NULL);
				}
			}
		}

		/*
		 * Add permissions
		 */
		$this->allow("admin", \Nette\Security\Permission::ALL);
	}


	/**
	 * Allows one or more Roles access to [certain $privileges upon] the specified Resource(s).
	 * If $assertion is provided, then it must return TRUE in order for rule to apply.
	 *
	 * @param  string|array|Permission::ALL  roles
	 * @param  string|array|Permission::ALL  resources
	 * @param  string|array|Permission::ALL  privileges
	 * @param  callback    assertion
	 * @return Permission  provides a fluent interface
	 */
	public function allowWithRecursion($resourceEntity, $roles, $resources, $privileges, $assertion = NULL)
	{
		$this->allow($roles, $resources, $privileges, $assertion);
		
		/* recursion */
		if($resourceEntity->parent){
			$this->allowWithRecursion($resourceEntity->parent, $roles, $resourceEntity->parent->name, self::ALL);
		}
	}
	
	
	/**
	 * @param string
	 * @param string
	 * @return array
	 */
	public function getClassResource($class)
	{
		$ref = new \Nette\Reflection\ClassType($class);

		if($ref->hasAnnotation('allowed')){
			return $ref->getAnnotation("allowed");
		}
		return NULL;
	}
	
	/**
	 * @param string
	 * @param string
	 * @return array
	 */
	public function getMethodResource($class, $method)
	{
		if(!method_exists($class, $method)){
			return NULL;
		}
		
		$ref = new \Nette\Reflection\Method($class, $method);

		if($ref->hasAnnotation('allowed')){
			return $ref->getAnnotation("allowed");
		}
		return NULL;
	}

}
