<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Modules;

use Venne;

/**
 * @author Josef Kříž
 */
class BaseService extends \Nette\DI\Container {

	/** @var \Nette\DI\Container */
	protected $container;
	
	protected $className;

	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(\Nette\DI\Container $container)
	{
		$this->container = $container;
	}
	
	/**
	 * @return \Nette\DI\Container
	 */
	public function getContainer()
	{
		return $this->container;
	}


	public function getEntityManager()
	{
		return $this->container->entityManager;
	}
	
	public function getRepository()
	{
		return $this->container->entityManager->getRepository("\\Venne\\CMS\\Modules\\" . ucfirst($this->className));
	}


	/**
	 * @param \Venne\Models\BaseEntity
	 * @param array|\Traversable
	 * @throws \Nette\InvalidArgumentException
	 */
	public function fillData($entity, $values)
	{
		if (!is_array($values) && !$values instanceof \Traversable) {
			throw new \Nette\InvalidArgumentException("Values must be array or Traversable");
		}
		foreach ($values as $key => $value) {
			$entity->{$key} = $value;
		}
	}


	public function create($values = array(), $withoutFlush = false)
	{
		$entity = new $this->className;
		$em = $this->getEntityManager();
		$em->persist($entity);
		$this->fillData($entity, $values);
		if(!$withoutFlush){
			$em->flush();
		} 
		return $entity;
	}
	
	public function update($entity, $values = array(), $withoutFlush = false)
	{
		$em = $this->getEntityManager();
		$this->fillData($entity, $values);
		if(!$withoutFlush){
			$em->flush();
		} 
	}
	
	public function remove($entity, $withoutFlush = false)
	{
		$em = $this->getEntityManager();
		$em->remove($entity);
		if(!$withoutFlush){
			$em->flush();
		} 
	}
	
}
