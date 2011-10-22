<?php

namespace App\RepositoryModule\AdminModule;

use Nette\Utils\Html;

/**
 * @allowed(administration-navigation)
 */
class DefaultPresenter extends \Venne\Developer\Presenter\AdminPresenter {


	/** @persistent */
	public $id;


	public function startup()
	{
		parent::startup();

		$this->addPath("Repository", $this->link(":Repository:Admin:Default:"));
		$this->template->items = $this->context->services->repository->getRepositories();
	}
	
	public function actionEdit()
	{
		$this->addPath("edit" . " (" . $this->id . ")", $this->link(":Repository:Admin:Default:edit"));
	}
	
	public function createComponentForm($name)
	{
		$form = new \RepositoryModule\RepositoryForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Repository has been saved");
		$form->setSubmitLabel("Create");
		return $form;
	}
	
	public function createComponentFormEdit($name)
	{
		$form = new \RepositoryModule\RepositoryForm($this, $name, $this->getParam("id"));
		$form->setSuccessLink("default");
		$form->setFlashMessage("Repository has been updated");
		$form->setSubmitLabel("Update");
		return $form;
	}

	public function handleDelete($key)
	{
		$this->context->services->repository->removeRepository($key);
		$this->flashMessage("Repository has been deleted", "success");
		$this->redirect("this");
	}

	public function beforeRender()
	{
		parent::beforeRender();
		$this->setTitle("Venne:CMS | Repositories administration");
		$this->setKeywords("repositories administration");
		$this->setDescription("Repositories administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}