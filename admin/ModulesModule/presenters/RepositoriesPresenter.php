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
class RepositoriesPresenter extends BasePresenter
{

	/** @persistent */
	public $key;
	
	public function startup()
	{
		parent::startup();
		$this->addPath("Repositories", $this->link(":Modules:Repositories:"));
	}
	
	public function actionDefault()
	{
		$this->template->items = $this->getModel()->getRepositories();
	}
	
	public function actionCreate()
	{
		$this->addPath("new item", $this->link(":Modules:Repositories:create"));
	}
	
	public function createComponentForm($name)
	{
		$form = new \Venne\CMS\Modules\ModulesRepositoriesForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Repository has been added");
		$form->addSubmit("submit", "Add");
		return $form;
	}
	
	public function createComponentFormEdit($name)
	{
		$form = new \Venne\CMS\Modules\ModulesRepositoriesForm($this, $name, $this->getParam("key"));
		$form->setSuccessLink("this");
		$form->setFlashMessage("Repository has been updated");
		$form->addSubmit("submit", "Update");
		return $form;
	}
	
	public function handleDelete($id)
	{
		$this->getModel()->removeRepository($id);
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