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
class ModulesEditForm extends \Venne\Forms\EditForm {


	public function startup()
	{
		parent::startup();
		
		//if($this->presenter->context->services->{$this->module} instanceof \Venne\CMS\Developer\IFrontModule){
			$this->addGroup("Routing");
			$this->addText("routePrefix", "Prefix");
		//}
	}


	public function load()
	{
		$container = $this->getPresenter()->getContext();
		
		//if($this->getPresenter()->getContext()->cms->{$this->module} instanceof \Venne\CMS\Developer\IFrontModule){
			$this["routePrefix"]->setValue($container->params["modules"][$this->key]["routePrefix"]);
		//}
	}

	public function save()
	{
		$model = $this->presenter->context->services->modules;
		$container = $this->getPresenter()->getContext();
		$values = $this->getValues();
		
		$model->saveModuleRoutePrefix($this->key, $values["routePrefix"]);
	}

}
