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
class ModulesEditForm extends \Venne\CMS\Developer\Form\BaseForm {

	protected $module;
	
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $module = NULL)
	{
		$this->module = $module;
		parent::__construct($parent, $name);
	}
	
	public function startup()
	{
		parent::startup();
		
		if($this->getPresenter()->getContext()->{$this->module} instanceof \Venne\CMS\Developer\IFrontModule){
			$this->addGroup("Routing");
			$this->addText("routePrefix", "Prefix");
		}
	}


	public function load()
	{
		$container = $this->getPresenter()->getContext();
		
		if($this->getPresenter()->getContext()->{$this->module} instanceof \Venne\CMS\Developer\IFrontModule){
			$this["routePrefix"]->setValue($container->params["venne"]["modules"][$this->module."Module"]["routePrefix"]);
		}
	}

	public function save()
	{
		$model = $this->getPresenter()->getContext()->moduleManager;
		$container = $this->getPresenter()->getContext();
		$values = $this->getValues();
		
		if($this->getPresenter()->getContext()->{$this->module} instanceof \Venne\CMS\Developer\IFrontModule){
			$model->saveModuleRoutePrefix($this->module, $values["routePrefix"]);
		}
	}

}
