<?php

namespace RepositoryModule;

use Nette\Utils\Html;

/**
 * @allowed(administration-navigation)
 */
class DefaultPresenter extends \Venne\CMS\Developer\Presenter\AdminPresenter {


	/** @persistent */
	public $id;


	public function startup()
	{
		parent::startup();

		$this->addPath("Repository", $this->link(":Repository:Default:"));
		$this->template->items = $this->getModel()->getRepositories();
	}
	
	public function actionEdit()
	{
		$this->addPath("edit" . " (" . $this->id . ")", $this->link(":Repository:Default:edit"));
	}
	
	public function createComponentForm($name)
	{
		$form = new \Venne\CMS\Modules\RepositoryForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Repository has been saved");
		$form->setSubmitLabel("Create");
		return $form;
	}
	
	public function createComponentFormEdit($name)
	{
		$form = new \Venne\CMS\Modules\RepositoryForm($this, $name, $this->getParam("id"));
		$form->setSuccessLink("default");
		$form->setFlashMessage("Repository has been updated");
		$form->setSubmitLabel("Update");
		return $form;
	}

	public function handleDelete($key)
	{
		$this->getModel()->removeRepository($key);
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