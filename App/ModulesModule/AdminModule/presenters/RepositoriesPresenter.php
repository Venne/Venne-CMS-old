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
 */
class RepositoriesPresenter extends BasePresenter
{

	/** @persistent */
	public $key;
	
	public function startup()
	{
		parent::startup();
		$this->addPath("Repositories", $this->link(":Modules:Admin:Repositories:"));
	}
	
	public function actionDefault()
	{
		$this->template->items = $this->context->services->modules->getRepositories();
	}
	
	public function actionCreate()
	{
		$this->addPath("new item", $this->link(":Modules:Admin:Repositories:create"));
	}
	
	public function createComponentForm($name)
	{
		$form = new \App\ModulesModule\ModulesRepositoriesForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Repository has been added");
		$form->setSubmitLabel("Add");
		return $form;
	}
	
	public function createComponentFormEdit($name)
	{
		$form = new \App\ModulesModule\ModulesRepositoriesForm($this, $name, $this->getParam("key"));
		$form->setSuccessLink("this");
		$form->setFlashMessage("Repository has been updated");
		$form->setSubmitLabel("Update");
		return $form;
	}
	
	public function handleDelete($id)
	{
		$this->context->services->modules->removeRepository($id);
		$this->flashMessage("Repository has been removed", "success");
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
