<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace AdminModule\SecurityModule;

/**
 * @author Josef Kříž
 * @resource AdminModule\SecurityModule\Users
 */
class UsersPresenter extends BasePresenter {


	/** @persistent */
	public $id;


	public function startup()
	{
		parent::startup();
		$this->addPath("Users", $this->link(":Admin:Security:Users:"));
		$this->template->table = $this->context->services->user->getRepository()->findAll();
	}


	public function actionCreate()
	{
		$this->addPath("new item", $this->link(":Admin:Security:Users:create"));
	}


	public function actionEdit()
	{
		$this->addPath("edit" . " (" . $this->id . ")", $this->link(":Admin:Security:Users:edit"));
	}


	public function handleDelete($id)
	{
		$item = $this->context->services->user->repository->find($id);
		$em = $this->context->doctrineContainer->entityManager;
		$em->remove($item);
		$em->flush();
		$this->flashMessage("User has been deleted", "success");
		$this->redirect("this");
	}


	public function createComponentForm($name)
	{
		$form = new \Venne\SecurityModule\UserForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("User has been created");
		$form->setSubmitLabel(" Create");
		return $form;
	}


	public function createComponentFormEdit($name)
	{
		$form = new \Venne\SecurityModule\UserForm($this, $name, $this->context->services->user->getRepository()->find($this->id));
		$form->setSuccessLink("this");
		$form->setFlashMessage("Website has been updated");
		$form->setSubmitLabel(" Update");
		return $form;
	}


	public function renderDefault()
	{
		
	}

}
