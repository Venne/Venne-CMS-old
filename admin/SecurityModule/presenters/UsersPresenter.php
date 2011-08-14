<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace SecurityModule;

/**
 * @author Josef Kříž
 * @allowed(administration-security-users)
 */
class UsersPresenter extends BasePresenter {


	/** @persistent */
	public $id;


	public function startup()
	{
		parent::startup();
		$this->addPath("Users", $this->link(":Security:Users:"));
		$this->template->table = $this->getContext()->entityManager->getRepository($this->getContext()->params["venneModulesNamespace"] . "Users")->findAll();
	}


	/**
	 * @allowed(administration-security-users-edit)
	 */
	public function actionCreate()
	{
		$this->addPath("new item", $this->link(":Security:Users:create"));
	}


	/**
	 * @allowed(administration-security-users-edit)
	 */
	public function actionEdit()
	{
		$this->addPath("edit" . " (" . $this->id . ")", $this->link(":Security:Users:edit"));
	}


	/**
	 * @allowed(administration-security-users-edit)
	 */
	public function handleDelete($id)
	{
		$this->getContext()->website->remove($this->getContext()->users->getRepository()->find($id));
		$this->flashMessage("User has been deleted", "success");
		$this->redirect("this");
	}


	public function createComponentForm($name)
	{
		$form = new \Venne\CMS\Modules\UserForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("User has been created");
		$form->setSubmitLabel(" Create");
		return $form;
	}


	public function createComponentFormEdit($name)
	{
		$form = new \Venne\CMS\Modules\UserForm($this, $name);
		$form->setEntity($this->getContext()->users->getRepository()->find($this->id));
		$form->setSuccessLink("this");
		$form->setFlashMessage("Website has been updated");
		$form->setSubmitLabel(" Update");
		return $form;
	}


	public function renderDefault()
	{
		
	}

}
