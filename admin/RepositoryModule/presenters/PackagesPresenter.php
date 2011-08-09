<?php

namespace RepositoryModule;

use Nette\Utils\Html;

/**
 * @allowed(administration-navigation)
 */
class PackagesPresenter extends \Venne\CMS\Developer\Presenter\AdminPresenter {


	/** @persistent */
	public $repository;
	
	protected $repositories;


	public function startup()
	{
		parent::startup();
		
		$this->repositories = $this->getModel()->getRepositories();
		if(!$this->repository){
			$this->repository = reset($this->repositories);
		}
		
		$this->addPath("Repository", $this->link(":Repository:Default:"));
		$this->template->items = $this->getModel()->getPackages($this->getParam("repository"));
	}
	
	public function createComponentFormPackage($name)
	{
		$form = new \Venne\CMS\Modules\RepositoryUploadForm($this, $name, $this->getParam("repository"));
		$form->setSuccessLink("default");
		$form->setFlashMessage("Package has been uploaded");
		$form->addSubmit("submit", "Upload");
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
		$this->getModel()->removePackage($key, $this->getParam("repository"));
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