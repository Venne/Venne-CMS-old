<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace ModulesModule;

/**
 * @author Josef Kříž
 */
class DefaultPresenter extends BasePresenter
{

	/** @persistent */
	public $key;
	
	public function actionDefault()
	{
		$this->template->items = array();
		$items = $this->getContext()->moduleManager->getAvailableModules();
		foreach($items as $item){
			$this->template->items[$item] = $this->getContext()->moduleManager->getModuleInfo($item);
		}
	}
	
	public function createComponentForm($name)
	{
		$form = new \Venne\CMS\Modules\ModulesEditForm($this, $name, $this->getParam("key"));
		$form->setSuccessLink("default");
		$form->setFlashMessage("Changes has been saved");
		$form->addSubmit("submit", "Save");
		return $form;
	}
	
	public function handleActivate($key)
	{
		$this->getContext()->moduleManager->activateModule($key);
		$this->flashMessage("Module has been activated", "success");
		$this->redirect("this");
	}
	
	public function handleDeactivate($key)
	{
		$this->getContext()->moduleManager->deactivateModule($key);
		$this->flashMessage("Module has been deactivated", "success");
		$this->redirect("this");
	}
	
	public function handleInstall($key)
	{
		$this->getContext()->moduleManager->installModule($key);
		$this->flashMessage("Module has been installed", "success");
		$this->redirect("this");
	}
	
	public function handleUninstall($key)
	{
		$this->getContext()->moduleManager->uninstallModule($key);
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
