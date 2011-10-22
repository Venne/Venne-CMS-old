<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\NavigationModule;

use Venne;

/**
 * @author Josef Kříž
 */
class Service extends Venne\Developer\Service\DoctrineService {

	public $entityNamespace = "\\App\\NavigationModule\\";
	
	/** @var array() */
	protected $path = array();

	/** @var \Venne\Modules\Navigation */
	protected $rootItems;

	/** @var \Venne\Modules\Navigation */
	protected $frontRootItems;
	
	/** @var \Venne\Application\Container */
	protected $context;

	public function __construct($context, $moduleName, \Doctrine\ORM\EntityManager $entityManager)
	{
		$this->context = $context;
		parent::__construct($moduleName, $entityManager);
	}

	public function hookAdminMenu($menu)
	{
		$nav = new NavigationEntity("Navigation");
		$nav->addKey("module", "Navigation:Admin");
		$menu[] = $nav;
	}
	
		
	/**
	 * @param \Nette\Http\Request $httpRequest
	 * @return \Venne\Modules\Navigation
	 */
	public function getRootItems()
	{
		if (!isset($this->rootItems)) {
			$repo = $this->getRepository();

			$website = $this->context->services->website->current;
			$this->rootItems = $repo->findBy(array("parent" => NULL));
		}
		return $this->rootItems;
	}
	
	/**
	 * @param string $name
	 * @param string $url 
	 */
	public function addPath($name, $url)
	{
		$data = new PathItem;
		$data->setName($name);
		$data->setUrl($url);
		$this->path[] = $data;
	}


	/**
	 * @return array 
	 */
	public function getPaths()
	{
		return $this->path;
	}
	
	/**
	 * @param \Nette\Http\Request $httpRequest
	 * @param bool $without
	 * @param int $layer
	 * @param int $depend
	 * @return array
	 */
	public function getCurrentList($without = Null, $layer = 0, $depend = Null)
	{
		$em = $this->getEntityManager();
		$data = array();
		$text = "";
		if (!$depend)
			$menu = $em->createQuery('SELECT u FROM \App\NavigationModule\NavigationEntity u WHERE u.parent IS NULL')
					->getResult();
		else
			$menu = $em->createQuery('SELECT u FROM \App\NavigationModule\NavigationEntity u WHERE u.parent= :depend ')
					->setParameters(array("depend" => $depend))
					->getResult();
		for ($i = 0; $i <= $layer; $i++) {
			$text .= "--";
		}
		foreach ($menu as $item) {
			if ($item->id != $without) {
				$data[$item->id] = $text . "- " . $item->name;
				$data += $this->getList($without, $layer + 1, $item->id);
			}
		}
		return $data;
	}


	/**
	 * @param \Nette\Http\Request $httpRequest
	 * @param int $layer
	 * @param int $depend
	 * @return array
	 */
	public function getList($without = Null, $layer = 0, $depend = Null)
	{
		$em = $this->getEntityManager();
		$data = array();
		$text = "";
		if (!$depend)
			$menu = $em->createQuery('SELECT u FROM \App\NavigationModule\NavigationEntity u WHERE u.parent IS NULL')->getResult();
		else
			$menu = $em->createQuery('SELECT u FROM \App\NavigationModule\NavigationEntity u WHERE u.parent= :depend ')
					->setParameters(array("depend" => $depend))
					->getResult();
		for ($i = 0; $i <= $layer; $i++) {
			$text .= "--";
		}
		foreach ($menu as $item) {
			if ($item->id != $without) {
				$data[$item->id] = $text . "- " . $item->name;
				$data += $this->getList($without, $layer + 1, $item->id);
			}
		}
		return $data;
	}
	
	/**
	 * Save structure
	 * @param array $data
	 */
	public function setStructure($data)
	{
		foreach ($data as $item) {
			foreach ($item as $item2) {
				$entity = $this->getRepository()->find($item2["id"]);
				$entity->parent = $this->getRepository()->find($item2["navigation_id"]);
				$entity->order = $item2["order"];
			}
		}
		$this->getEntityManager()->flush();
	}
	
	/**
	 * @param integer $website_id
	 * @param integer $parent_id 
	 * @return integer
	 */
	public function getOrderValue($parent_id = NULL)
	{
		if ($parent_id) {
			$query = $this->getEntityManager()->createQuery('SELECT MAX(u.order) FROM \App\NavigationModule\NavigationEntity u WHERE u.parent = ?2')->setParameter(2, $parent_id);
		} else {
			$query = $this->getEntityManager()->createQuery('SELECT MAX(u.order) FROM \App\NavigationModule\NavigationEntity u WHERE u.parent is NULL');
		}
		return $query->getSingleScalarResult() + 1;
	}
	
	public function addModuleItem($moduleName, $moduleItemId, $name, $parent_id, $paramsArray, $withoutFlush = false)
	{
		$entity = new NavigationEntity;
		$entity->order = $this->getOrderValue($parent_id);
		$entity->moduleName = $moduleName;
		$entity->moduleItemId = $moduleItemId;
		$entity->name = $name;
		if($parent_id){
			$entity->parent = $this->getRepository()->find($parent_id);
		}
		$entity->type = NavigationEntity::TYPE_LINK;
		$this->getEntityManager()->persist($entity);

		foreach ($paramsArray as $key => $value) {
			$entityKey = new NavigationKeyEntity;
			$entityKey->navigation = $entity;
			$entityKey->key = $key;
			$entityKey->val = $value;
			$this->getEntityManager()->persist($entityKey);
		}

		if (!$withoutFlush) {
			$this->getEntityManager()->flush();
		}
	}


	public function updateModuleItem(\App\NavigationModule\NavigationEntity $menuEntity, $moduleName, $moduleItemId, $name, $parent_id, $paramsArray, $withoutFlush = false)
	{
		$menuEntity->moduleName = $moduleName;
		$menuEntity->moduleItemId = $moduleItemId;
		$menuEntity->name = $name;
		if($parent_id){
			$menuEntity->parent = $this->getRepository()->find($parent_id);
		}

		foreach ($menuEntity->keys as $value) {
			$this->getEntityManager()->remove($value);
			unset($value);
		}

		foreach ($paramsArray as $key => $value) {
			$entityKey = new NavigationKeyEntity;
			$entityKey->key = $key;
			$entityKey->val = $value;
			$menuEntity->keys = $entityKey;
			$this->getEntityManager()->persist($entityKey);
		}

		if (!$withoutFlush) {
			$this->getEntityManager()->flush();
		}
	}


	public function delete(\Venne\Developer\Doctrine\BaseEntity $entity, $withoutFlush = false)
	{
		$query = $this->getEntityManager()->createQuery('SELECT u FROM \App\NavigationModule\NavigationEntity u WHERE u.parent = ?1 AND u.order > ?2')->setParameter(1, isset($entity->parent->id) ? $entity->parent->id : NULL)->setParameter(2, $entity->order);
		foreach ($query->getResult() as $item) {
			$item->order = $item->order - 1;
		}
		return parent::delete($entity, $withoutFlush);
	}

}

