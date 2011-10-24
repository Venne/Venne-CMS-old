<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\RepositoryModule;

use Venne\ORM\Column;
use Nette\Utils\Html;
use Venne\Forms\Form;

/**
 * @author Josef Kříž
 */
class RepositoryForm extends \Venne\Forms\EditForm {

	public function startup()
	{
		parent::startup();

		$this->addGroup("Repository informations");
		$this->addText("name", "Name")->addRule(self::FILLED, "Enter name");

	}


	public function load()
	{
		$model = $this->presenter->context->services->repository;
			
		$this["name"]->setValue($this->key);
	}

	public function save()
	{
		$values = $this->getValues();
		$model = $this->presenter->context->services->repository;

		if($this->key){
			$model->renameRepository($values["name"], $this->key);
		}else{
			$model->saveRepository($values["name"]);
		}
	}

}
