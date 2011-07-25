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
		$this->addGroup();
		$this->addText("name", "Website name")->addRule(Form::FILLED, 'Enter website name');
		$this->addText("template", "Template")
				->setDefaultValue("venne")
				->addRule(Form::FILLED, 'Enter template');
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
		$presenter = $this->getPresenter();
		$service = $presenter->getContext()->website;
		$em = $service->getEntityManager();
		
		if(!$this->entity){
			$this->entity = new Website;
			$entityLang = new Language;
			$entityLang->website = $this->entity;
			$em->persist($this->entity);
			$em->persist($entityLang);
			$this->mapToEntity($this->entity);
			
			$entityLang->lang = $values["lang"];
			$entityLang->name = $values["langName"];
			$entityLang->alias = $values["langAlias"];
			
			$em->flush();
			$this->entity->langDefault = $entityLang->id;
		}else{
			$this->mapToEntity($this->entity);
			$entityLang = $presenter->getContext()->language->getRepository()->find($this->entity->langDefault);
			$entityLang->lang = $values["lang"];
			$entityLang->name = $values["langName"];
			$entityLang->alias = $values["langAlias"];
		}
		$em->flush();
	}

}
