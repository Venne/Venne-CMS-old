<?php

namespace ErrorModule;

use Nette\Forms\Form;
use Nette\Web\Html;

class DefaultPresenter extends \Venne\CMS\Developer\Presenter\AdminPresenter {


	/** @persistent */
	public $id;

	public function startup()
	{
		parent::startup();
		$this->addPath("Error", $this->link(":Error:Default:"));
	}

	/**
	 * @allowed(administration-pages-edit)
	 */
	public function actionCreate()
	{
		$this->addPath("new item", $this->link(":Error:Default:create"));
	}

	/**
	 * @allowed(administration-pages-edit)
	 */
	public function actionEdit()
	{
		$this->addPath("Edit ({$this->id})", $this->link(":Error:Default:edit"));
	}


	public function createComponentForm($name)
	{
		$form = new \Venne\CMS\Modules\ErrorForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Error has been created");
		$form->setSubmitLabel("Create");
		return $form;
	}


	public function createComponentFormEdit($name)
	{
		$form = new \Venne\CMS\Modules\ErrorForm($this, $name, $this->getParam("id"));
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


/**
	 * @allowed(administration-pages-edit)
	 */
	public function handleDelete($id)
	{
		$this->getModel()->removeError($id);
		$this->flashMessage("Page has been deleted", "success");
		$this->redirect("this", array("id" => NULL));
	}


	public function renderDefault()
	{
		$this->template->table = $this->getModel()->getErrors();
	}

}