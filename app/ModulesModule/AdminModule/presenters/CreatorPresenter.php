<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace ModulesModule\AdminModule;

/**
 * @author Josef Kříž
 */
class CreatorPresenter extends BasePresenter {

	/** @persistent */
	public $key;

	public function startup()
	{
		parent::startup();
		$this->addPath("Creator", $this->link(":Modules:Admin:Creator:"));
	}
	
	public function actionCreate()
	{
		$this->addPath("new item", $this->link(":Modules:Admin:Creator:create"));
	}
	
	public function actionDefault()
	{
		$this->template->items = $this->getModel()->getPackageBuilds();
		foreach($this->template->items as $key=>$item){
			$this->template->items[$key] = $this->getModel()->loadPackageBuild($key);
		}
	}

	public function createComponentFormCreator($name)
	{
		$form = new \Venne\Modules\ModulesCreatorForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Script has been saved");
		$form->setSubmitLabel("Create");
		return $form;
	}
	
	public function createComponentFormCreatorEdit($name)
	{
		$form = new \Venne\Modules\ModulesCreatorForm($this, $name, $this->getParam("key"));
		$form->setSuccessLink("this");
		$form->setFlashMessage("Script has been updated");
		$form->setSubmitLabel("Update");
		return $form;
	}
	
	public function handleBuild($id)
	{
		if($this->getModel()->buildPackage($id)){
			$this->flashMessage("Package has been builded", "success");
		}else{
			$this->flashMessage("Building failed", "warning");
		}
		$this->redirect("this");
	}


	public function handleDelete($id)
	{
		$this->getModel()->removePackageBuild($id);
		$this->flashMessage("Script has been deleted", "success");
		$this->redirect("this");
	}


	public function renderDefault()
	{
		
	}

}
