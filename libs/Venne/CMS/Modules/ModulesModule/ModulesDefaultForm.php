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
class ModulesDefaultForm extends \Venne\CMS\Developer\Form\BaseForm {

	public function startup()
	{
		parent::startup();
		$model = $this->getPresenter()->getContext()->moduleManager;
		
		
		$this->addGroup("Default modules");
		$this->addSelect("defaultModule", "Default module")->setItems($model->getFrontModules(), false);
		$this->addSelect("defaultErrorModule", "Error module")->setItems($model->getErrorModules(), false);
	}


	public function load()
	{
		$this["defaultModule"]->setDefaultValue($this->getPresenter()->getContext()->params["venne"]["defaultModule"]);
		$this["defaultErrorModule"]->setDefaultValue($this->getPresenter()->getContext()->params["venne"]["defaultErrorModule"]);
	}

	public function save()
	{
		$this->getPresenter()->getContext()->moduleManager->saveDefaultModule($this["defaultModule"]->getValue());
		$this->getPresenter()->getContext()->moduleManager->saveDefaultErrorModule($this["defaultErrorModule"]->getValue());
	}

}
