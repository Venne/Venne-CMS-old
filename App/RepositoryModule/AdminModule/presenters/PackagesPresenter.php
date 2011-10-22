<?php

namespace App\RepositoryModule\AdminModule;

use Nette\Utils\Html;

/**
 * @allowed(administration-navigation)
 */
class PackagesPresenter extends \Venne\Developer\Presenter\AdminPresenter {


	/** @persistent */
	public $repository;
	
	protected $repositories;


	public function startup()
	{
		parent::startup();
		
		$this->repositories = $this->context->services->repository->getRepositories();
		if(!$this->repository){
			$this->repository = reset($this->repositories);
		}
		
		$this->addPath("Repository", $this->link(":Repository:Admin:Default:"));
		$this->template->items = $this->context->services->repository->getPackages($this->getParam("repository"));
	}
	
	public function createComponentFormPackage($name)
	{
		$form = new \RepositoryModule\RepositoryUploadForm($this, $name, $this->getParam("repository"));
		$form->setSuccessLink("default");
		$form->setFlashMessage("Package has been uploaded");
		$form->setSubmitLabel("Upload");
		return $form;
	}
	
	public function createComponentForm($name)
	{
		$form = new \Venne\Application\UI\Form($this, $name);
		$form->addSelect("repository", "Repository", $this->repositories)->setDefaultValue($this->getParam("repository"));
		$form->onSuccess[] = \callback($this, "setRepository");
		return $form;
	}

	public function setRepository($form)
	{
		$this->repository = $form["repository"]->getValue();
		$this->redirect("this");
	}

	public function handleDelete($key)
	{
		$this->context->services->repository->removePackage($key, $this->getParam("repository"));
		$this->flashMessage("Package has been deleted", "success");
		$this->redirect("this");
	}

	public function beforeRender()
	{
		parent::beforeRender();
		$this->setTitle("Venne:CMS | Repositories administration");
		$this->setKeywords("repositories administration");
		$this->setDescription("Repositories administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
		
		$this->template->showSelect = true;
	}

}