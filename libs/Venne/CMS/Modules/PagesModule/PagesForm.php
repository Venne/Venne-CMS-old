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
class PagesForm extends \Venne\CMS\Developer\Form\ContentEntityForm {


	public function startup()
	{
		parent::startup();
		
		$model = $this->getPresenter()->getContext()->pages->model;
		
		$this->addGroup();
		$this->addText("title", "Title")
				->setRequired('Enter title');
		$this->addText("keywords", "Keywords");
		$this->addText("description", "Description");

		$this->addGroup("Dates");
		$this->addDateTime("created", "Created")->setDefaultValue(new \Nette\DateTime);
		$this->addDateTime("updated", "Updated")->setDefaultValue(new \Nette\DateTime);

		$this->addGroup("URL");
		$this->addCheckbox("mainPage", "Main page");
		$this->addText("url", "URL")
				->addRule(callback($model, "isUrlAvailable"), "This URL is used.")
				->setOption("description", "(example: 'contact')");
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
		$model = $this->getPresenter()->getContext()->pages->model;

		$this->entity = $model->saveItem(
					$this->entity, $values["title"], $values["url"], $values["text"],
					$values["mainPage"], $values["keywords"], $values["description"],
					$values["created"], $values["updated"]
				);		
	}


	protected function getLinkParams()
	{
		return array(
			"module"=>"Pages",
			"presenter"=>"Default",
			"action"=>"default",
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
