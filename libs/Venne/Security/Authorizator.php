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


	const ROLE = 'role';
	const RESOURCE = 'resource';
	const PRIVILEGE = 'privilege';

	/** @var \Nette\DI\Container */
	private $container;
	protected $defaultRoles = array();
	protected $resourceTree = array();

	/** @var array */
	protected $privileges;

	/** @var array  Resource storage */
	private $resources = array();


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
		if ($identity instanceof Venne\SecurityModule\UserEntity) {
			$identity2 = $this->container->services->user->getRepository()->find($identity->id);
			$identity->setRoles($identity2->getRoles());
			$identity->setData($identity2->getData());
		}

		/*
		 * Add resources
		 */
		$this->addResource("adminpanel");
		$this->addResource("AdminModule");
		foreach ($this->container->params["modules"] as $key => $module) {
			$this->container->modules->$key->setPermissions($container, $this);
		}

		/*
		 * Add roles
		 */
		$roles = array();
		$res = $this->container->services->role->getRepository()->findAll();
		foreach ($res as $item) {
			$this->addRole($item->name, $item->parent ? $item->parent->name : NULL);
			if (in_array($item->name, $this->container->user->roles)) {
				$roles[] = $item->id;
			}
		}

		/*
		 * Setup permissions
		 */
		$permissions = $this->container->services->permission->repository->findAll();
		foreach ($permissions as $permission) {
			if ($this->hasResource($permission->resource)) {
				if ($permission->allow) {
					$this->allow($permission->role->name, $permission->resource, $permission->privilege ? $permission->privilege : NULL);
				} else {
					$this->deny($permission->role->name, $permission->resource, $permission->privilege ? $permission->privilege : NULL);
				}
			}
		}
		$this->allow("admin", \Nette\Security\Permission::ALL);
	}


	public function getResources()
	{
		return $this->resourceTree;
	}


	public function getPrivileges()
	{
		return $this->privileges;
	}


	public function addResource($resource, $parent = NULL)
	{
		if ($parent) {
			$this->resourceTree[$parent][] = $resource;
		} else {
			$this->resourceTree["root"][] = $resource;
		}
		parent::addResource($resource, $parent);
	}


	public function addPrivilege($resourceName, $privileges)
	{
		if (!isset($this->privileges[$resourceName])) {
			$this->privileges[$resourceName] = array();
		}
		$this->privileges[$resourceName] = array_merge($this->privileges[$resourceName], (array) $privileges);
	}


	/**
	 * @param string
	 * @param string
	 * @return array
	 */
	public static function parseAnnotations($class, $method = NULL)
	{
		if (strpos($class, '::') !== FALSE && !$method) {
			list($class, $method) = explode('::', $class);
		}

		$ref = new \Nette\Reflection\Method($class, $method);
		$cRef = new \Nette\Reflection\ClassType($class);

		$resource = $ref->hasAnnotation('resource') ? $ref->getAnnotation('resource') : ($cRef->hasAnnotation('resource') ? $cRef->getAnnotation('resource') : NULL);

		$privilege = $ref->hasAnnotation('privilege') ? $ref->getAnnotation('privilege') : NULL;

		return array(
			static::RESOURCE => $resource,
			static::PRIVILEGE => $privilege,
		);
	}

}
