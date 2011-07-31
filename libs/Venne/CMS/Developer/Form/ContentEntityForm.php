<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Developer\Form;

/**
 * @author     Josef Kříž
 */
class ContentEntityForm extends EntityForm{


	/**
	 * Application form constructor.
	 */
	public function startup()
	{
		parent::startup();
		foreach ($this->getPresenter()->getContext()->moduleManager->getContentExtensionModules() as $module) {
			$this->addGroup("Module $module settings")->setOption('container', \Nette\Utils\Html::el('fieldset')->class('collapsible collapsed'));
			$container = $this->addContainer("module_$module");
			$this->getPresenter()->getContext()->{$module}->contentExtension->setForm($container);
			$this->setCurrentGroup();
		}
	}


	public function onSubmitForm()
	{
		if (!$this->isValid()) {
			return;
		}

		if ($this->save() === NULL) {
			foreach ($this->getPresenter()->getContext()->moduleManager->getContentExtensionModules() as $module) {
				$container = $this->getComponent("module_$module");
				$this->getPresenter()->getContext()->{$module}->contentExtension->saveForm($container, $this->getModuleName(), $this->getModuleItemId(), $this->getLinkParams());
				$this->removeComponent($container);
			}

			if ($this->flash) {
				$this->getPresenter()->flashMessage($this->flash, "success");
			}
			if ($this->successLink) {
				$this->presenter->redirect($this->successLink);
			}
		}
	}


	public function setEntity($entity)
	{
		$this->entity = $entity;
		if (!$this->isSubmitted()) {
			$this->setValuesFromEntity();

			foreach ($this->getPresenter()->getContext()->moduleManager->getContentExtensionModules() as $module) {
				$container = $this->getComponent("module_$module");
				$this->getPresenter()->getContext()->{$module}->contentExtension->setValues($container, $this->getModuleName(), $this->getModuleItemId(), $this->getLinkParams());
			}
		}
	}


	protected function getLinkParams()
	{
		return array();
	}


	protected function getModuleName()
	{
		return "";
	}


	protected function getModuleItemId()
	{
		return NULL;
	}

}