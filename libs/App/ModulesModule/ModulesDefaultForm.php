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
class ModulesDefaultForm extends \Venne\Developer\Form\EditForm {

	public function startup()
	{
		parent::startup();
		$model = $this->presenter->context->services->modules;
		
		
		$this->addGroup("Default modules");
		$this->addSelect("defaultModule", "Default module")->setItems($model->getFrontModules(), false);
		$this->addSelect("defaultErrorModule", "Error module")->setItems($model->getFrontModules(), false);
		
		$this["defaultModule"]->setDefaultValue($this->presenter->context->params["website"]["defaultModule"]);
		$this["defaultErrorModule"]->setDefaultValue($this->presenter->context->params["website"]["defaultErrorModule"]);
	}

	public function save()
	{
		$this->presenter->context->services->system->saveDefaultModule($this["defaultModule"]->getValue());
		$this->presenter->context->services->system->saveDefaultErrorModule($this["defaultErrorModule"]->getValue());
	}

}
