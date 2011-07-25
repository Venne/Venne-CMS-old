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
class NavigationContentExtension extends BaseService implements \Venne\CMS\Developer\IContentExtension {

	public function saveForm(\Nette\Forms\Container $container, $moduleName, $moduleItemId, $linkParams)
	{
		$values = $container->getValues();
		
		$menu = $this->container->navigation->getRepository()->findOneBy(
					array(
							"moduleItemId"=>$moduleItemId,
							"moduleName"=>$moduleName,
						)
				);
		
		if(!$menu){
			if($values["use"]){
				$this->container->navigation->getRepository()->addModuleItem($moduleName, $moduleItemId, $values["name"], $values["navigation_id"], $linkParams, $this->container->website->getCurrentFrontWebsite($this->container->httpRequest)->id);
			}
		}else{
			if($values["use"]){
				$this->container->navigation->getRepository()->updateModuleItem($menu, $moduleName, $moduleItemId, $values["name"], $values["navigation_id"], $linkParams, $this->container->website->getCurrentFrontWebsite($this->container->httpRequest)->id);
			}else{
				$this->container->navigation->getRepository()->removeModuleItem($menu);
			}
		}
		$this->container->navigation->getEntityManager()->flush();
	}


	public function setForm(\Nette\Forms\Container $container)
	{
		$container->addCheckbox("use", "Create navigation item");
		$container->addText("name", "Navigation name");
		$container->addSelect("navigation_id", "Navigation parent")
				->setItems($this->container->navigation->getCurrentFrontList($this->container->httpRequest))
				->setPrompt("root");
	}
	
	public function setValues(\Nette\Forms\Container $container, $moduleName, $moduleItemId, $linkParams)
	{
		$menu = $this->container->navigation->getRepository()->findOneBy(
					array(
							"moduleItemId"=>$moduleItemId,
							"moduleName"=>$moduleName,
						)
				);
		if($menu){
			$container["use"]->setValue(true);
			$container["name"]->setValue($menu->name);

			if(!$menu->parent){
				$container["navigation_id"]->setValue(NULL);
			}else{
				$container["navigation_id"]->setValue($menu->parent->id);
			}
		}else{
			$container["use"]->setValue(false);
		}
	}

}
