<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace PagesModule;

use Venne\ORM\Column;
use Nette\Utils\Html;
use Venne\Forms\Form;
/**
 * @author Josef Kříž
 */
class PagesForm extends \Venne\Developer\Form\ContentEntityForm {


	public function startup()
	{
		parent::startup();
		
		$model = $this->presenter->context->services->pages;
		
		$this->addGroup();
		$this->addText("title", "Title")
				->setRequired('Enter title');
		$this->addText("keywords", "Keywords");
		$this->addText("description", "Description");
		$this->addSelect("layout", "Layout");

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

	public function save()
	{
		if($this->key){
			$this->presenter->context->services->pages->update($this->key, $this->getValues());
		}else{
			$this->key = $this->presenter->context->services->pages->create($this->getValues());
			$this->presenter->context->doctrineContainer->entityManager->persist($this->key);
		}
		$this->presenter->context->doctrineContainer->entityManager->flush();
	}


	protected function getLinkParams()
	{
		return array(
			"module"=>"Pages",
			"presenter"=>"Default",
			"action"=>"default",
			"url"=>$this->key->url
			);
	}


	protected function getModuleName()
	{
		return "pages";
	}


	protected function getModuleItemId()
	{
		return $this->key->id;
	}

}
