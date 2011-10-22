<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\ModulesModule;

use Venne\ORM\Column;
use Nette\Utils\Html;
use Venne\Forms\Form;

/**
 * @author Josef Kříž
 */
class ModulesUploadForm extends \Venne\Developer\Form\EditForm {

	public function startup()
	{
		parent::startup();
		$model = $this->presenter->context->services->modules;
		
		$this->addGroup("Upload package to local repository");
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
		$model = $this->getPresenter()->getContext()->cms->modules->model;
		
		$values = $this->getValues();
		if($values["file"]->isOk()){
			$model->uploadPackage($values["file"]);
		}
	}

}
