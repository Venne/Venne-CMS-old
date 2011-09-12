<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Modules;

use Venne\ORM\Column;
use Nette\Utils\Html;
use Venne\Forms\Form;

/**
 * @author Josef Kříž
 */
class ModulesCreatorForm extends \Venne\Developer\Form\BaseForm {

	protected $id;
	
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $id = NULL)
	{
		$this->id = $id;
		parent::__construct($parent, $name);
	}	

	public function startup()
	{
		parent::startup();

		$this->addGroup("Package informations");
		$this->addText("pkgname","Package name")->addRule(self::FILLED, "Enter package name");
		$this->addText("pkgver", "Package version")->addRule(self::FILLED, "Enter package version");
		$this->addText("pkgdesc", "Package description")->addRule(self::FILLED, "Enter package description");
		$this->addText("licence", "Licence");
		$this->addTag("dependencies", "Dependencies");
		
		$this->addGroup("Packager");
		$this->addText("packager", "Packager name")->addRule(self::FILLED, "Enter packager name");
		
		$this->addGroup("Files");
		$this->addTextArea("files", "Files");
	}


	public function load()
	{
		if($this->id){
			$model = $this->getPresenter()->getContext()->cms->modules->model;
			
			$values = $model->loadPackageBuild($this->id);
			$values["files"] = join("\n", $values["files"]);
						
			$this->setValues($values);
			$this["dependencies"]->setDefaultValue($values["dependencies"]);
		}
	}

	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$model = $this->getPresenter()->getContext()->cms->modules->model;

		$values["files"] = str_replace("\r", "", $values["files"]);
		$values["files"] = explode("\n", $values["files"]);
		
		$model->savePackageBuild($values['pkgname'], $values['pkgver'], $values['pkgdesc'], $values['licence'], $values['dependencies'], $values['packager'], $values['files']);
	}

}
