<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Modules;

use Venne\ORM\Column;
use Nette\Utils\Html;
use Venne\Forms\Form;

/**
 * @author Josef Kříž
 */
class RepositoryUploadForm extends \Venne\CMS\Developer\Form\BaseForm {

	protected $repository;
	
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $repository = NULL)
	{
		$this->repository = $repository;
		parent::__construct($parent, $name);
	}
	
	public function startup()
	{
		parent::startup();
		$model = $this->getPresenter()->getContext()->modules->model;
		
		$this->addGroup("Upload package to repository");
		$this->addUpload("file", "Package")
			->addRule(self::FILLED, "Enter file")
			->addRule(self::MIME_TYPE, 'Bad mime type', 'application/zip')
			->addRule(callback($model, "isUploadPackageValid"), "This zip archive is not package");
	}


	public function load()
	{

	}

	public function save()
	{
		parent::save();
		$model = $this->getPresenter()->getContext()->repository->model;
		
		$values = $this->getValues();
		if($values["file"]->isOk()){
			$model->uploadPackage($values["file"], $this->repository);
		}
	}

}
