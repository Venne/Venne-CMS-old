<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Modules;

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 */
class AliasContentExtension implements \Venne\Developer\ContentExtension\IContentExtension {

	/** @var \Venne\NavigationModule\Service */
	protected $service;


	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(\Venne\NavigationModule\Service $service)
	{
		$this->service = $service;
	}

	public function hookContentExtensionSave(\Nette\Forms\Container $form, $moduleName, $moduleItemId, $linkParams)
	{
		$values = $form["module_alias"]->getValues();
		$model = $this->container->cms->alias->model;
		
		$model->saveItems($moduleItemId, $moduleName, $values, $linkParams);
	}


	public function hookContentExtensionForm(\Nette\Forms\Container $form)
	{
		$form->addGroup("Navigation settings")->setOption('container', \Nette\Utils\Html::el('fieldset')->class('collapsible collapsed'));
		$container = $form->addContainer("module_alias");
		
		$container->addTag("urls", "URL alias");
		
		$form->setCurrentGroup();
	}


	public function hookContentExtensionLoad(\Nette\Forms\Container $form, $moduleName, $moduleItemId, $linkParams)
	{
		$model = $this->container->cms->alias->model;
		
		$container["urls"]->setDefaultValue($model->getItems($moduleItemId, $moduleName));
	}
	
	public function hookContentExtensionRemove($moduleName, $moduleItemId)
	{
	}
	
	public function hookContentExtensionRender($presenter, $moduleName)
	{
		
	}

}
