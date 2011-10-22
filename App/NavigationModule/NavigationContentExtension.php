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

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 */
class NavigationContentExtension implements \Venne\Developer\ContentExtension\IContentExtension {


	/** @var \NavigationModule\Service */
	protected $service;


	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(\App\NavigationModule\Service $service)
	{
		$this->service = $service;
	}


	public function hookContentExtensionSave(\Nette\Forms\Container $form, $moduleName, $moduleItemId, $linkParams)
	{
		$values = $form["module_navigation"]->getValues();

		$menu = $this->service->getRepository()->findOneBy(
				array(
					"moduleItemId" => $moduleItemId,
					"moduleName" => $moduleName,
				)
		);

		if (!$menu) {
			if ($values["use"]) {
				$this->service->addModuleItem($moduleName, $moduleItemId, $values["name"], $values["navigation_id"], $linkParams);
			}
		} else {
			if ($values["use"]) {
				$this->service->updateModuleItem($menu, $moduleName, $moduleItemId, $values["name"], $values["navigation_id"], $linkParams);
			} else {
				$this->service->removeModuleItem($menu);
			}
		}
	}


	public function hookContentExtensionForm(\Nette\Forms\Container $form)
	{
		$form->addGroup("Navigation settings")->setOption('container', \Nette\Utils\Html::el('fieldset')->class('collapsible collapsed'));
		$container = $form->addContainer("module_navigation");

		$container->addCheckbox("use", "Create navigation item");
		$container->addText("name", "Navigation name");
		$container->addSelect("navigation_id", "Navigation parent")
				->setItems($this->service->getCurrentList())
				->setPrompt("root");

		$form->setCurrentGroup();
	}


	public function hookContentExtensionLoad(\Nette\Forms\Container $form, $moduleName, $moduleItemId, $linkParams)
	{
		$container = $form["module_navigation"];

		$menu = $this->service->getRepository()->findOneBy(
				array(
					"moduleItemId" => $moduleItemId,
					"moduleName" => $moduleName,
				)
		);
		if ($menu) {
			$container["use"]->setValue(true);
			$container["name"]->setValue($menu->name);

			if (!$menu->parent) {
				$container["navigation_id"]->setValue(NULL);
			} else {
				$container["navigation_id"]->setValue($menu->parent->id);
			}
		} else {
			$container["use"]->setValue(false);
		}
	}


	public function hookContentExtensionRemove($moduleName, $moduleItemId)
	{
		$menu = $this->service->getRepository()->findOneBy(
				array(
					"moduleItemId" => $moduleItemId,
					"moduleName" => $moduleName,
				)
		);
		if ($menu) {
			$this->service->delete($menu);
		}
	}
	
	public function hookContentExtensionRender($presenter, $moduleName)
	{
		
	}

}
