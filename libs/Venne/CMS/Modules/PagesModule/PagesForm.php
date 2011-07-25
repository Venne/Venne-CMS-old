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

/**
 * @author Josef Kříž
 */
class PagesForm extends \Venne\Forms\ContentEntityForm {


	public function startup()
	{
		parent::startup();
		$this->addGroup();
		$this->addText("title", "Title");
		$this->addText("keywords", "Keywords");
		$this->addText("description", "Description");

		$this->addGroup("Dates");
		$this->addDateTime("created", "Created")->setDefaultValue(new \Nette\DateTime);
		$this->addDateTime("updated", "Updated")->setDefaultValue(new \Nette\DateTime);

		$this->addGroup("URL");
		$this->addCheckbox("mainPage", "Main page");
		$this->addText("url", "URL");

		$this->addGroup("Text");
		$this->addTextArea("text", "", Null, 20);
	}


	public function setValuesFromEntity()
	{
		parent::setValuesFromEntity();

		$current = NULL;

		$this["updated"]->setValue(new \Nette\DateTime);
		$a = new \Nette\DateTime();
	}


	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$presenter = $this->getPresenter();
		$service = $presenter->getContext()->pages;
		$em = $service->getEntityManager();
		$website = $presenter->getContext()->website->getCurrentFrontWebsite($presenter->getContext()->httpRequest);

		if (!$this->entity) {
			$this->entity = new Pages;
			$em->persist($this->entity);
		}
		$this->mapToEntity($this->entity);

		/* set main Page */
		if ($values["mainPage"]) {
			foreach ($service->getRepository()->findByWebsite($website->id) as $page) {
				$page->mainPage = false;
			}
			$this->entity->mainPage = true;
		}

		$this->entity->website = $website;
		
		$em->flush();
	}


	protected function getLinkParams()
	{
		return array(
			"module"=>"Pages",
			"url"=>$this->entity->url
			);
	}


	protected function getModuleName()
	{
		return "pages";
	}


	protected function getModuleItemId()
	{
		return $this->entity->id;
	}

}
