<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\ModulesModule\AdminModule;

/**
 * @author Josef Kříž
 * 
 * @resource AdminModule\ModulesModule
 */
class DefaultPresenter extends BasePresenter
{

	/** @persistent */
	public $key;
	
	public function actionDefault()
	{
		$this->template->items = array();
		$items = $this->context->services->modules->getAvailableModules();
		foreach($items as $item){
			$this->template->items[$item] = $this->context->services->modules->getModuleInfo($item);
		}
	}
	
	public function createComponentDefaultForm($name)
	{
		$form = new \App\ModulesModule\ModulesDefaultForm($this, $name);
		$form->setSuccessLink("default");
		$form->setSubmitLabel("Save");
		$form->setFlashMessage("Changes has been saved");
		return $form;
	}
	
	public function createComponentForm($name)
	{
		$form = new \Venne\Modules\ModulesEditForm($this, $name, $this->getParam("key"));
		$form->setSuccessLink("default");
		$form->setFlashMessage("Changes has been saved");
		return $form;
	}
	
	public function handleActivate($key)
	{
		$this->context->services->modules->activateModule($key);
		$this->flashMessage("Module has been activated", "success");
		$this->redirect("this");
	}
	
	public function handleDeactivate($key)
	{
		$this->context->services->modules->deactivateModule($key);
		$this->flashMessage("Module has been deactivated", "success");
		$this->redirect("this");
	}
	
	public function handleInstall($key)
	{
		$this->context->services->modules->installModule($key);
		$this->flashMessage("Module has been installed", "success");
		$this->redirect("this");
	}
	
	public function handleUninstall($key)
	{
		$this->context->services->modules->uninstallModule($key);
		$this->flashMessage("Module has been uninstalled", "success");
		$this->redirect("this");
	}

	public function renderDefault()
	{
		$this->setTitle("Venne:CMS | Module administration");
		$this->setKeywords("module administration");
		$this->setDescription("Module administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}
