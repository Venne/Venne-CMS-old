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
class AliasModel extends Venne\CMS\Developer\Model {
	
	/**
	 * @param integer $moduleItemId
	 * @param string $moduleName
	 * @param array $values
	 * @param array $linkParams 
	 */
	public function saveItems($moduleItemId, $moduleName, $values, $linkParams)
	{
		$items = $this->getRepository()->findBy(
						array(
							"moduleItemId" => $moduleItemId,
							"moduleName" => $moduleName,
						)
		);

		if ($items) {
			foreach ($items as $item) {
				$this->getEntityManager()->remove($item);
			}
		}
		
		foreach ($values["urls"] as $item) {
			$entity = new Alias();
			$entity->moduleName = $moduleName;
			$entity->moduleItemId = $moduleItemId;
			$entity->url = $item;
			$this->getEntityManager()->persist($entity);

			foreach($linkParams as $key=>$value){
				$entityKey = new AliasKey();
				$entity->keys = $entityKey;
				$entityKey->key = $key;
				$entityKey->val = $value;
				$this->getEntityManager()->persist($entityKey);
			}
		}
		$this->getEntityManager()->flush();
	}
	
	/**
	 * @param integer $moduleItemId
	 * @param string $moduleName
	 * @return array
	 */
	public function getItems($moduleItemId, $moduleName)
	{
		$items = $this->getRepository()->findBy(
						array(
							"moduleItemId" => $moduleItemId,
							"moduleName" => $moduleName,
						)
		);
		$arr = array();
		foreach ($items as $item) {
			$arr[] = $item->url;
		}
		return $arr;
	}
	
}