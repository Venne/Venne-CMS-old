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
use Nette\Forms\Form;

/**
 * @author Josef Kříž
 */
class WebsiteForm extends \Venne\Forms\EntityForm{
	
	public function startup()
	{
		parent::startup();
		$model = $this->getPresenter()->getContext()->website->model;
		
		$this->addGroup();
		$this->addText("name", "Website name")
				->addRule(Form::FILLED, 'Enter website name')
				->addRule(callback($model, "isNameAvailable"), "This name is used.");
		$this->addText("skin", "Skin")
				->setDefaultValue("venne")
				->addRule(Form::FILLED, 'Enter skin');
		$this->addText("regex", "Regex")
				->setDefaultValue("*")
				->addRule(Form::FILLED, 'Enter regex');
		
		$this->addGroup("Routing");
		$this->addText("routePrefix");

		$this->addGroup();
		$this->addSelect("langType", "Language route type", array("get"=>"GET", "url"=>"URL"));
		$this->addText("langValue", "Language value")->setDefaultValue("lang");
		
		$this->addGroup("Default language");
		$this->addText("lang", "Language")->setDefaultValue("cs");
		$this->addText("langAlias", "Language alias")->setDefaultValue("www");
		$this->addText("langName", "Language name")->setDefaultValue("čeština");
	}
	
	public function setValuesFromEntity()
	{
		parent::setValuesFromEntity();
		$entity = $this->presenter->getContext()->language->getRepository()->find($this->entity->langDefault);
		$this["lang"]->setValue($entity->lang);
		$this["langAlias"]->setValue($entity->alias);
		$this["langName"]->setValue($entity->name);
	}


	public function save()
	{
		$values = $this->getValues();
		$model = $this->getPresenter()->getContext()->website->model;
		$langModel = $this->getPresenter()->getContext()->language->model;
		
		/*
		 * Update language
		 */
		$langEntity = $this->entity ? $this->getPresenter()->getContext()->language->getRepository()->find($this->entity->langDefault) : NULL; 
		$langEntity = $langModel->saveItem($langEntity, $values["lang"], $values["langName"], $values["langAlias"], $this->entity);
		
		
		$this->entity = $model->saveItem(
					$this->entity, $values["name"], $values["regex"], $values["skin"],
					$values["langType"], $values["langValue"], $langEntity->id, $values["routePrefix"]
				);
		
		$langEntity = $langModel->saveItem($langEntity, $values["lang"], $values["langName"], $values["langAlias"], $this->entity);
	}

}
