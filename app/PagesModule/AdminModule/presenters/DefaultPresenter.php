<?php

namespace App\PagesModule\AdminModule;

use Nette\Forms\Form;
use Nette\Web\Html;

/**
 * @resource AdminModule\PagesModule
 */
class DefaultPresenter extends \Venne\Developer\Presenter\AdminPresenter {


	/** @persistent */
	public $id;

	/**
	 * @privilege read
	 */
	public function startup()
	{
		parent::startup();
		$this->addPath("Pages", $this->link(":Pages:Admin:Default:"));
	}

	public function actionCreate()
	{
		$this->addPath("new item", $this->link(":Pages:Admin:Default:create"));
	}

	public function actionEdit()
	{
		$this->addPath("Edit ({$this->id})", $this->link(":Pages:Admin:Default:edit"));
	}


	public function createComponentForm($name)
	{
		$form = new \App\PagesModule\PagesForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Page has been created");
		$form->setSubmitLabel("Create");
		return $form;
	}


	public function createComponentFormEdit($name)
	{
		$form = new \App\PagesModule\PagesForm($this, $name, $this->presenter->context->services->pages->getRepository()->find($this->id));
		$form->setSuccessLink("this");
		$form->setFlashMessage("Page has been updated");
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
		$this->context->services->pages->delete($this->presenter->context->services->pages->getRepository()->find($this->id));
		$this->flashMessage("Page has been deleted", "success");
		$this->redirect("this", array("id" => NULL));
	}


	public function renderDefault()
	{
		$this->template->table = $this->presenter->context->services->pages->getRepository()->findAll();
	}

}