<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\CommentsModule;

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 */
class CommentsContentExtension implements \Venne\Developer\ContentExtension\IContentExtension {


	/** @var \CommentsModule\Service */
	protected $service;


	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(\App\CommentsModule\Service $service)
	{
		$this->service = $service;
	}


	public function hookContentExtensionForm(\Nette\Forms\Container $form)
	{
		$form->addGroup("Comments settings")->setOption('container', \Nette\Utils\Html::el('fieldset')->class('collapsible collapsed'));
		$container = $form->addContainer("module_comments");

		$container->addCheckbox("use", "Allow comments")->setDefaultValue(false);

		$form->setCurrentGroup();
	}


	public function hookContentExtensionSave(\Nette\Forms\Container $form, $moduleName, $moduleItemId, $linkParams)
	{
		$values = $form["module_comments"]->getValues();

		$this->service->saveSetting($moduleItemId, $moduleName, $values["use"]);
	}


	public function hookContentExtensionLoad(\Nette\Forms\Container $form, $moduleName, $moduleItemId, $linkParams)
	{
		$form["module_comments"]["use"]->setValue($this->service->getSetting($moduleItemId, $moduleName));
	}


	public function hookContentExtensionRemove($moduleName, $moduleItemId)
	{
		$entity = $this->service->getRepository()->findOneBy(array("moduleName" => $moduleName, "moduleItemId" => $moduleItemId));
		if ($entity) {
			$this->service->delete($entity);
		}
	}
	
	public function hookContentExtensionRender($presenter, $moduleName)
	{
		$presenter->getWidget("element_comments_"."moduleSEP".$moduleName."SEP".$presenter->contentExtensionsKey)->render();
	}

}
