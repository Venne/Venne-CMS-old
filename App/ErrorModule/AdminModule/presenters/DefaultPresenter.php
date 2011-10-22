<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\ErrorModule\AdminModule;

use Nette\Forms\Form;
use Nette\Web\Html;

/**
 * @author Josef Kříž
 * 
 * @resource AdminModule\ErrorModule
 */
class DefaultPresenter extends \Venne\Developer\Presenter\AdminPresenter {


	/** @persistent */
	public $id;


	public function startup()
	{
		parent::startup();
		$this->addPath("Error", $this->link(":Error:Admin:Default:"));
	}


	public function actionCreate()
	{
		$this->addPath("new item", $this->link(":Error:Admin:Default:create"));
	}


	public function actionEdit()
	{
		$this->addPath("edit ({$this->id})", $this->link(":Error:Admin:Default:edit"));
	}


	public function createComponentForm($name)
	{
		$form = new \App\ErrorModule\ErrorForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Error has been created");
		$form->setSubmitLabel("Create");
		return $form;
	}


	public function createComponentFormEdit($name)
	{
		$form = new \App\ErrorModule\ErrorForm($this, $name, $this->getParam("id"));
		$form->setSuccessLink("this");
		$form->setFlashMessage("Error has been updated");
		$form->setSubmitLabel("Update");
		return $form;
	}


	public function beforeRender()
	{
		parent::beforeRender();
		$this->setTitle("Venne:CMS | Pages administration");
		$this->setKeywords("pages administration");
		$this->setDescription("pages administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}


	public function handleDelete($id)
	{
		$this->context->services->error->removeError($id);
		$this->flashMessage("Page has been deleted", "success");
		$this->redirect("this", array("id" => NULL));
	}


	public function renderDefault()
	{
		$this->template->table = $this->context->services->error->getErrors();
	}

}