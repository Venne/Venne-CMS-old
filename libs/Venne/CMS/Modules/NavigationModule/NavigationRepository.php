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
class NavigationRepository extends BaseRepository {

	public function addModuleItem($moduleName, $moduleItemId, $name, $parent_id, $paramsArray, $website_id, $withoutFlush = false)
	{
		$entity = new Navigation;
		$entity->moduleName = $moduleName;
		$entity->moduleItemId = $moduleItemId;
		$entity->name = $name;
		$entity->parent = $this->find($parent_id);
		$entity->website = $this->getEntityManager()->getRepository(VENNE_MODULES_NAMESPACE . "Website")->find($website_id);
		$entity->type = Navigation::TYPE_LINK;
		$this->getEntityManager()->persist($entity);
		
		foreach($paramsArray as $key=>$value){
			$entityKey = new NavigationKey;
			$entityKey->navigation = $entity;
			$entityKey->key = $key;
			$entityKey->val = $value;
			$this->getEntityManager()->persist($entityKey);
		}
		
		if(!$withoutFlush){
			$this->getEntityManager()->flush();
		}
	}
	
	public function updateModuleItem(\Venne\CMS\Modules\Navigation $menuEntity, $moduleName, $moduleItemId, $name, $parent_id, $paramsArray, $website_id, $withoutFlush = false)
	{
		$menuEntity->moduleName = $moduleName;
		$menuEntity->moduleItemId = $moduleItemId;
		$menuEntity->name = $name;
		$menuEntity->parent = $this->find($parent_id);
		$menuEntity->website = $this->getEntityManager()->getRepository(VENNE_MODULES_NAMESPACE . "Website")->find($website_id);
		
		foreach($menuEntity->keys as $value){
			$this->getEntityManager()->remove($value);
			unset($value);
		}
		
		foreach($paramsArray as $key=>$value){
			$entityKey = new NavigationKey;
			$entityKey->key = $key;
			$entityKey->val = $value;
			$menuEntity->keys = $entityKey;
			$this->getEntityManager()->persist($entityKey);
		}
		
		if(!$withoutFlush){
			$this->getEntityManager()->flush();
		}
	}
	
	public function removeModuleItem(\Venne\CMS\Modules\Navigation $menuEntity)
	{
		$this->getEntityManager()->remove($menuEntity);
		
		if(!$withoutFlush){
			$this->getEntityManager()->flush();
		}
	}
	
}

