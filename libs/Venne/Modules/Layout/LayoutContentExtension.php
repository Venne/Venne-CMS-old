<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\LayoutModule;

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 */
class LayoutContentExtension implements \Venne\Developer\ContentExtension\IContentExtension {

	/** @var \Venne\LayoutModule\Service */
	protected $service;


	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(\Venne\LayoutModule\Service $service)
	{
		$this->service = $service;
	}
	
	public function hookContentExtensionSave(\Nette\Forms\Container $form, $moduleName, $moduleItemId, $linkParams)
	{
		$values = $form["module_layout"]->getValues();
		
		$this->service->saveLayout($moduleItemId, $moduleName, $values["use"], $values["layout"], $linkParams);
	}


	public function hookContentExtensionForm(\Nette\Forms\Container $form)
	{
		$form->addGroup("Layout settings")->setOption('container', \Nette\Utils\Html::el('fieldset')->class('collapsible collapsed'));
		$container = $form->addContainer("module_layout");
		
		$container->addCheckbox("use", "Set layout")->setDefaultValue(false);
		$container->addText("layout", "Layout");
		//$container->addSelect("layout", "Layout", $this->container->cms->moduleManager->getLayouts());
		
		$form->setCurrentGroup();
	}


	public function hookContentExtensionLoad(\Nette\Forms\Container $form, $moduleName, $moduleItemId, $linkParams)
	{
		$container = $form["module_layout"];
		$values = $this->service->loadLayout($moduleItemId, $moduleName);
		
		if($values){
			$container["use"]->setValue(true);
			$container["layout"]->setValue($values->layout);
		}
	}
	
	public function hookContentExtensionRemove($moduleName, $moduleItemId)
	{
		
	}
	
	public function hookContentExtensionRender($presenter, $moduleName)
	{
		
	}

}
