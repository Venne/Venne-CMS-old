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
	
	public function startup()
	{
		$this->sendResponse($this->getContext()->repository->model->sendPackage($this->getParam("package"), $this->getParam("repository")));
	}
	

	
}