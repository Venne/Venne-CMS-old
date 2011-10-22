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
class ModulesRepositoriesForm extends \Venne\Developer\Form\EditForm {


	public function startup()
	{
		parent::startup();

		$this->addGroup("Repository");
		$this->addText("name", "Name")->addRule(self::FILLED, "Enter name");
		$this->addTextArea("mirrors", "Mirrors")->addRule(self::FILLED, "Enter mirrors");

		$this->addGroup("User account");
		$this->addCheckbox("anonym", "Use anonym login")->setDefaultValue(true);
		$this->addText("username", "User name")
				->addConditionOn($this["anonym"], self::EQUAL, 0)
				->addCondition(self::EQUAL, "Enter user name");
		$this->addPassword("password", "Password");
	}


	public function load()
	{
		$model = $this->presenter->context->services->modules;

		$config = $model->getRepositoryInfo($this->key);
		if($config){
			$this["name"]->setValue($this->key);
			$this["mirrors"]->setValue(join("\n", $config["mirrors"]));

			if (isset($config["name"])) {
				$this["username"]->setValue($config["name"]);
			}

			if (isset($config["password"])) {
				$this["password"]->setValue($config["password"]);
			}
		}
	}


	public function save()
	{
		$values = $this->getValues();
		$model = $this->presenter->context->services->modules;

		$values["mirrors"] = str_replace("\r", "", $values["mirrors"]);
		$values["mirrors"] = explode("\n", $values["mirrors"]);
		$model->saveRepository($values["name"], $values["mirrors"], $values["anonym"] ? $values["username"] : NULL, $values["anonym"] ? $values["password"] : NULL);
	}

}
