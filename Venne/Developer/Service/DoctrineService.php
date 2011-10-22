<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Service;

use Venne;

/**
 * @author Josef Kříž
 * @author	Patrik Votoček
 */
class DoctrineService extends BaseService {


	/** @var \Doctrine\ORM\EntityManager */
	public $entityManager;
	public $entityNamespace = "\\App\\";


	public function __construct($moduleName, \Doctrine\ORM\EntityManager $entityManager)
	{
		parent::__construct($moduleName);
		$this->entityManager = $entityManager;
	}


	public function getEntityManager()
	{
		return $this->entityManager;
	}


	public function getRepository()
	{
		return $this->getEntityManager()->getRepository($this->entityNamespace . ucfirst($this->moduleName . "Entity"));
	}


	/**
	 * @return mixed
	 */
	protected function createEntityPrototype()
	{
		$class = $this->entityNamespace . ucfirst($this->moduleName) . "Entity";
		return new $class;
	}


	/**
	 * @param \Venne\Developer\Doctrine\BaseEntity
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


	/**
	 * @param array|\Traversable
	 * @return IEntity
	 * @throws \Nette\InvalidArgumentException
	 */
	public function create($values = array(), $withoutFlush = false)
	{
		if (!is_array($values) && !$values instanceof \Traversable) {
			throw new \Nette\InvalidArgumentException("Values must be array or Traversable");
		}

		$entity = $this->createEntityPrototype();
		$this->fillData($entity, $values);
		$this->entityManager->persist($entity);

		if (!$withoutFlush) {
			$this->entityManager->flush();
		}

		return $entity;
	}


	/**
	 * @param IEntity
	 * @param array|\Traversable
	 * @return IEntity
	 * @throws \Nette\InvalidArgumentException
	 */
	public function update(\Venne\Developer\Doctrine\BaseEntity $entity, $values, $withoutFlush = false)
	{
		$this->fillData($entity, $values);
		
		if (!$withoutFlush) {
			$em = $this->getEntityManager();
			$em->flush();
		}
		
		return $entity;
	}


	/**
	 * @param \Venne\Developer\Doctrine\BaseEntity
	 * @param bool
	 * @return \Venne\Developer\Doctrine\BaseEntity
	 * @throws \Nella\Models\Exception
	 * @throws \Nella\Models\EmptyValueException
	 * @throws \Nella\Models\DuplicateEntryException
	 */
	public function delete(\Venne\Developer\Doctrine\BaseEntity $entity, $withoutFlush = false)
	{
		$em = $this->getEntityManager();
		$em->remove($entity);
		if (!$withoutFlush) {
			$em->flush();
		}
		return $entity;
	}

}
