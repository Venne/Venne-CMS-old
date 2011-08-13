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
class ErrorForm extends \Venne\CMS\Developer\Form\baseForm {

	protected $id;
	
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $id = NULL)
	{
		$this->id = $id;
		parent::__construct($parent, $name);
	}

	public function startup()
	{
		parent::startup();

		$this->addGroup();
		if(!$this->id){
			$this->addText("code", "Code")->addRule(self::FILLED, "Enter code");
		}
		$this->addTextArea("text", "Text");
	}


	public function load()
	{
		if($this->id){
			$values = $this->getPresenter()->getContext()->error->model->getError($this->id);
			
			$this["text"]->setValue($values->text);
		}
	}


	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$model = $this->getPresenter()->getContext()->error->model;

		if($this->id){
			$values["code"] = $this->getPresenter()->getContext()->error->model->getError($this->id)->code;
		}
		
		$model->saveError($values["code"], $values["text"]);
	}

}
