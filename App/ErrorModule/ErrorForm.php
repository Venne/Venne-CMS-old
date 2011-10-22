<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\ErrorModule;

use Venne\ORM\Column;
use Nette\Utils\Html;
use Venne\Forms\Form;

/**
 * @author Josef Kříž
 */
class ErrorForm extends \Venne\Developer\Form\EditForm {

	public function startup()
	{
		parent::startup();

		$this->addGroup();
		if (!$this->key) {
			$this->addText("code", "Code")->addRule(self::FILLED, "Enter code");
		}
		$this->addTextArea("text", "Text");
	}


	public function load()
	{
		$values = $this->presenter->context->services->error->getError($this->key);

		$this["text"]->setValue($values->text);
	}


	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$model = $this->presenter->context->services->error;

		if ($this->key) {
			$values["code"] = $model->getError($this->id)->code;
		}

		$model->saveError($values["code"], $values["text"]);
	}

}
