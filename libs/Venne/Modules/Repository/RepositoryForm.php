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
class RepositoryForm extends \Venne\Developer\Form\BaseForm {

	protected $id;
	
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $id = NULL)
	{
		$this->id = $id;
		parent::__construct($parent, $name);
	}	

	public function startup()
	{
		parent::startup();

		$this->addGroup("Repository informations");
		$this->addText("name", "Name")->addRule(self::FILLED, "Enter name");

	}


	public function load()
	{
		if($this->id){
			$model = $this->getPresenter()->getContext()->repository->model;
			
			$this["name"]->setValue($this->id);
		}
	}

	public function save()
	{
		$values = $this->getValues();
		$model = $this->getPresenter()->getContext()->repository->model;

		if($this->id){
			$model->renameRepository($values["name"], $this->id);
		}else{
			$model->saveRepository($values["name"]);
		}
	}

}
