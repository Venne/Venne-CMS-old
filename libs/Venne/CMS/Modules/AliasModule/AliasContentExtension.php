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

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 */
class AliasContentExtension extends BaseService implements \Venne\CMS\Developer\IContentExtension {


	public function saveForm(\Nette\Forms\Container $container, $moduleName, $moduleItemId, $linkParams)
	{
		$values = $container->getValues();

		$items = $this->container->alias->getRepository()->findBy(
						array(
							"moduleItemId" => $moduleItemId,
							"moduleName" => $moduleName,
						)
		);

		if ($items) {
			foreach ($items as $item) {
				$this->container->entityManager->remove($item);
			}
		}

		foreach ($values["urls"] as $item) {
			$entity = new Alias();
			$entity->moduleName = $moduleName;
			$entity->moduleItemId = $moduleItemId;
			$entity->url = $item;
			$this->container->entityManager->persist($entity);

			foreach($linkParams as $key=>$value){
				$entityKey = new AliasKey();
				$entity->keys = $entityKey;
				$entityKey->key = $key;
				$entityKey->val = $value;
				$this->container->entityManager->persist($entityKey);
			}
		}
		$this->container->entityManager->flush();
	}


	public function setForm(\Nette\Forms\Container $container)
	{
		$container->addTag("urls", "URL alias");
	}


	public function setValues(\Nette\Forms\Container $container, $moduleName, $moduleItemId, $linkParams)
	{
		$items = $this->container->alias->getRepository()->findBy(
						array(
							"moduleItemId" => $moduleItemId,
							"moduleName" => $moduleName,
						)
		);
		$arr = array();
		foreach ($items as $item) {
			$arr[] = $item->url;
		}
		$container["urls"]->setDefaultValue($arr);
	}

}
