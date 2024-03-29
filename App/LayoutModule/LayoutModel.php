<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\Modules;

use Venne;

/**
 * @author Josef Kříž
 */
class LayoutModel extends Venne\Developer\Model\DoctrineModel {


	public function detectLayout()
	{
		$presenter = $this->service->container->application->presenter;
		$name = $presenter->getName() . ":" . $presenter->getAction();
		$data = array();


		$query = $this->getEntityManager()->createQuery('
					SELECT u FROM \Venne\Modules\Layout u WHERE u.website = ?1 ORDER BY u.regex DESC
				')->setParameter(1, $this->service->container->cms->website->current);

		foreach ($query->getResult() as $item) {
			$data[$item->regex][] = $item;
		}

		ksort($data);
		$data = array_reverse($data);
		
		foreach ($data as $items) {
			$layout = false;
			$params = -1;

			foreach ($items as $item) {
				if (strpos($name, $item->regex) !== false) {
					if (count($item->keys) > $params) {
						$ok = true;
						foreach ($item->keys as $key) {
							if ($key->val != $this->container->application->presenter->getParam($key->key)) {
								$ok = false;
								break;
							}
						}
						if ($ok) {
							$params = count($item->keys);
							$layout = $item->layout;
						}
					}
				}
			}

			if ($layout) {
				return $layout;
			}
		}

		return "layout";
	}


	public function getLayouts()
	{
		$website = $this->container->cms->website->currentFront;

		return $this->getRepository()->findBy(array("website" => $website->id));
	}


	public function removeLayout($id)
	{
		$this->getEntityManager()->remove($this->getRepository()->find($id));
		$this->getEntityManager()->flush();
	}


	public function saveLayout($moduleItemId, $moduleName, $use, $layout, $linkParams)
	{
		$entity = $this->getRepository()->findOneBy(array("moduleItemId" => $moduleItemId, "moduleName" => $moduleName));

		if (!$use) {
			if ($entity) {
				$this->getEntityManager()->remove($entity);
			}
		} else {
			if (!$entity) {
				$entity = new Layout;
				$this->getEntityManager()->persist($entity);
				$entity->moduleItemId = $moduleItemId;
				$entity->moduleName = $moduleName;
				$entity->regex = "";
				$entity->website = $this->container->cms->website->currentFront;

				dump($linkParams);
				
				if (isset($linkParams["module"])) {
					$entity->regex .= $linkParams["module"];
					if (isset($linkParams["presenter"])) {
						$entity->regex .= ":" . $linkParams["presenter"];
						if (isset($linkParams["action"])){
							$entity->regex .= ":" . $linkParams["action"];
						}
					}
				}
				
				foreach ($linkParams as $keyName => $param) {
					if($keyName == "module" || $keyName == "presenter" || $keyName == "action"){
						continue;
					}
					
					$key = new LayoutKey;
					$key->key = $keyName;
					$key->val = $param;
					$key->layout = $entity;
					$this->getEntityManager()->persist($key);
				}
			}
			$entity->layout = $layout;
		}
		$this->getEntityManager()->flush();
	}


	public function loadLayout($moduleItemId, $moduleName)
	{
		return $this->getRepository()->findOneBy(array("moduleItemId" => $moduleItemId, "moduleName" => $moduleName));
	}


	public function createLayout($layoutName, $module = NULL, $presenter = NULL, $action = NULL, $params = NULL, $moduleName = NULL, $moduleItemId = NULL)
	{
		$params = (array) $params;

		$layout = new Layout;
		$layout->layout = $layoutName;
		$layout->regex = "";
		$layout->website = $this->container->cms->website->currentFront;
		$layout->moduleName = $moduleName;
		$layout->moduleItemId = $moduleItemId;
		if ($module) {
			$layout->regex .= $module;
			if ($presenter) {
				$layout->regex .= ":" . $presenter;
				if ($action) {
					$layout->regex .= ":" . $action;
				}
			}
		}

		$this->getEntityManager()->persist($layout);

		foreach ($params as $keyName => $param) {
			$key = new LayoutKey;
			$key->key = $keyName;
			$key->val = $param;
			$key->layout = $layout;
			$this->getEntityManager()->persist($key);
		}

		$this->getEntityManager()->flush();
	}


	public function updateLayout($id, $layoutName, $module = NULL, $presenter = NULL, $action = NULL, $params = NULL)
	{
		$params = (array) $params;

		$layout = $this->getRepository()->find($id);

		/* Delete keys */
		foreach ($layout->keys as $key) {
			$this->getEntityManager()->remove($key);
		}

		$layout->layout = $layoutName;
		$layout->regex = "";
		if ($module) {
			$layout->regex .= $module;
			if ($presenter) {
				$layout->regex .= ":" . $presenter;
				if ($action) {
					$layout->regex .= ":" . $action;
				}
			}
		}

		$this->getEntityManager()->persist($layout);

		foreach ($params as $keyName => $param) {
			$key = new LayoutKey;
			$key->key = $keyName;
			$key->val = $param;
			$key->layout = $layout;
			$this->getEntityManager()->persist($key);
		}

		$this->getEntityManager()->flush();
	}


	public function getLayout($id)
	{
		return $this->getRepository()->find($id);
	}
	
	/**
	 * @param string $moduleName
	 * @param string $moduleItemId 
	 */
	public function removeItemByModuleName($moduleName, $moduleItemId)
	{
		$item = $this->getRepository()->findOneBy(array("moduleName"=>$moduleName, "moduleItemId"=>$moduleItemId));
		if($item){
			$this->getEntityManager()->remove($item);
			$this->getEntityManager()->flush();
		}
	}

}

