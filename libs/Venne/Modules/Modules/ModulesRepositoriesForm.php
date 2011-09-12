<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Modules;

use Venne\ORM\Column;
use Nette\Utils\Html;
use Venne\Forms\Form;

/**
 * @author Josef Kříž
 */
class ModulesRepositoriesForm extends \Venne\Developer\Form\BaseForm {


	protected $id;


	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $id = NULL)
	{
		$this->id = $id;
		parent::__construct($parent, $name);
	}


	public function startup()
	{
		parent::startup();
		$model = $this->getPresenter()->getContext()->cms->modules->model;


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
		if ($this->id) {
			$model = $this->getPresenter()->getContext()->cms->modules->model;

			$config = $model->getRepositoryInfo($this->id);
			$this["name"]->setValue($this->id);
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
		$model = $this->getPresenter()->getContext()->cms->modules->model;

		$values["mirrors"] = str_replace("\r", "", $values["mirrors"]);
		$values["mirrors"] = explode("\n", $values["mirrors"]);
		$model->saveRepository($values["name"], $values["mirrors"], $values["anonym"] ? $values["username"] : NULL, $values["anonym"] ? $values["password"] : NULL);
	}

}
