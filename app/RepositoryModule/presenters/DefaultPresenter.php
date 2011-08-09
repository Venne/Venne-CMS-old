<?php

namespace RepositoryModule;

use Nette\Environment;

/**
 * @allowed(module-pages)
 */
class DefaultPresenter extends \Venne\CMS\Developer\Presenter\FrontPresenter
{
	/** @persistent */
	public $repository;
	
	/** @persistent */
	public $package;
	
	public function __construct(Nette\ComponentModel\IContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);
	}
	
	public function createComponentFormPackage($name)
	{
		$form = new \Venne\CMS\Modules\RepositoryUploadForm($this, $name, $this->getParam("repository"));
		$form->setSuccessLink("default");
		$form->setFlashMessage("Package has been uploaded");
		$form->addSubmit("submit", "Upload");
		return $form;
	}
	
	public function startup()
	{
		if($this->repository && $this->package){
			$this->sendResponse($this->getContext()->repository->model->sendPackage($this->getParam("package"), $this->getParam("repository")));
		}
		
		parent::startup();
		
		$this->addPath("Repositories", $this->link(":Repository:Default:", array("repository"=>NULL)));
		
		if($this->repository){
			$this->addPath("Repository (" . $this->repository . ")", $this->link(":Repository:Default:"));
			
			$this->template->packages = $this->getModel()->getPackages($this->repository);
		}else{
			$this->template->repositories = $this->getModel()->getRepositories();
		}
	}
	

	
}