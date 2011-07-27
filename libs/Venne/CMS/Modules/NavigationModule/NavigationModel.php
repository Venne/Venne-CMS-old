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
class NavigationModel extends Venne\CMS\Developer\Model {


	/** @var array() */
	protected $path = array();
	/** @var \Venne\CMS\Modules\Navigation */
	protected $rootItems;
	/** @var \Venne\CMS\Modules\Navigation */
	protected $frontRootItems;


	/**
	 * @param \Nette\Http\Request $httpRequest
	 * @return \Venne\CMS\Modules\Navigation
	 */
	public function getRootItems()
	{
		if (!isset($this->rootItems)) {
			$repo = $this->getRepository();

			$website = $this->getContainer()->website->current;
			$this->rootItems = $repo->findBy(array("website" => $website->id, "parent" => NULL));
		}
		return $this->rootItems;
	}


	/**
	 * @param \Nette\Http\Request $httpRequest
	 * @return \Venne\CMS\Modules\Navigation
	 */
	public function getFrontRootItems()
	{
		if (!isset($this->frontRootItems)) {
			$repo = $this->getRepository();

			$website = $this->getContainer()->website->currentFront;
			$this->frontRootItems = $repo->findBy(array("website" => $website->id, "parent" => NULL));
		}
		return $this->frontRootItems;
	}


	public function addModuleItem($moduleName, $moduleItemId, $name, $parent_id, $paramsArray, $website_id, $withoutFlush = false)
	{
		$entity = new Navigation;
		$entity->moduleName = $moduleName;
		$entity->moduleItemId = $moduleItemId;
		$entity->name = $name;
		$entity->parent = $this->getRepository()->find($parent_id);
		$entity->website = $this->getEntityManager()->getRepository(VENNE_MODULES_NAMESPACE . "Website")->find($website_id);
		$entity->type = Navigation::TYPE_LINK;
		$this->getEntityManager()->persist($entity);

		foreach ($paramsArray as $key => $value) {
			$entityKey = new NavigationKey;
			$entityKey->navigation = $entity;
			$entityKey->key = $key;
			$entityKey->val = $value;
			$this->getEntityManager()->persist($entityKey);
		}

		if (!$withoutFlush) {
			$this->getEntityManager()->flush();
		}
	}


	public function updateModuleItem(\Venne\CMS\Modules\Navigation $menuEntity, $moduleName, $moduleItemId, $name, $parent_id, $paramsArray, $website_id, $withoutFlush = false)
	{
		$menuEntity->moduleName = $moduleName;
		$menuEntity->moduleItemId = $moduleItemId;
		$menuEntity->name = $name;
		$menuEntity->parent = $this->getRepository()->find($parent_id);
		$menuEntity->website = $this->getEntityManager()->getRepository(VENNE_MODULES_NAMESPACE . "Website")->find($website_id);

		foreach ($menuEntity->keys as $value) {
			$this->getEntityManager()->remove($value);
			unset($value);
		}

		foreach ($paramsArray as $key => $value) {
			$entityKey = new NavigationKey;
			$entityKey->key = $key;
			$entityKey->val = $value;
			$menuEntity->keys = $entityKey;
			$this->getEntityManager()->persist($entityKey);
		}

		if (!$withoutFlush) {
			$this->getEntityManager()->flush();
		}
	}


	public function removeModuleItem(\Venne\CMS\Modules\Navigation $menuEntity)
	{
		$this->getEntityManager()->remove($menuEntity);

		if (!$withoutFlush) {
			$this->getEntityManager()->flush();
		}
	}


	/**
	 * @param \Nette\Http\Request $httpRequest
	 * @param bool $without
	 * @param int $layer
	 * @param int $depend
	 * @return array
	 */
	public function getCurrentFrontList($httpRequest, $without = Null, $layer = 0, $depend = Null)
	{
		$em = $this->getEntityManager();
		$data = array();
		$text = "";
		if (!$depend)
			$menu = $em->createQuery('SELECT u FROM \Venne\CMS\Modules\Navigation u WHERE u.parent IS NULL AND u.website = :website')
					->setParameter("website", $this->getContainer()->website->currentFront->id)
					->getResult();
		else
			$menu = $em->createQuery('SELECT u FROM \Venne\CMS\Modules\Navigation u WHERE u.parent= :depend ')
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
			$menu = $em->createQuery('SELECT u FROM \Venne\CMS\Modules\Navigation u WHERE u.parent IS NULL')->getResult();
		else
			$menu = $em->createQuery('SELECT u FROM \Venne\CMS\Modules\Navigation u WHERE u.parent= :depend ')
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
	 * @param string $name
	 * @param string $url 
	 */
	public function addPath($name, $url)
	{
		$data = new Venne\CMS\Navigation\PathItem;
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

}
