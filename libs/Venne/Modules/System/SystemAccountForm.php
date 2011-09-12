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
class SystemAccountForm extends \Venne\Developer\Form\EditForm {

	protected $mode;

	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $mode = "common")
	{
		$this->mode = $mode;
		parent::__construct($parent, $name);
	}	
	
	public function startup()
	{
		parent::startup();

		$this->addGroup();
		$this->addHidden("section")->setDefaultValue($this->mode);
		if($this->mode != "common") $this->addCheckbox("use", "Use for this mode");
		$this->addText("name", "Name");
		$this->addPassword("password", "Password")->setOption("description", "minimal length is 5 char");
		$this->addPassword("password_confirm", "Confirm password");
		
		if($this->mode != "common") {
			$this["name"]
					->addConditionOn($this["use"], self::EQUAL, 1)
					->addRule(self::FILLED, 'Enter name');
			$this["password"]
					->addConditionOn($this["use"], self::EQUAL, 1)
					->addRule(self::FILLED, 'Enter password')
					->addRule(self::MIN_LENGTH, 'Password is short', 5);
			$this["password_confirm"]
					->addConditionOn($this["use"], self::EQUAL, 1)
					->addRule(self::EQUAL, 'Invalid re password', $this['password']);
		}else{
			$this["name"]
					->addRule(self::FILLED, 'Enter name');
			$this["password"]
					->addRule(self::FILLED, 'Enter password')
					->addRule(self::MIN_LENGTH, 'Password is short', 5);
			$this["password_confirm"]
					->addRule(self::EQUAL, 'Invalid re password', $this['password']);
		}
	}


	public function load()
	{
		$model = $this->presenter->context->services->system->model;
		
		$config = $model->loadAccount($this->mode);
				
		$this->setDefaults($config);
		
		if($this->mode != "common"){
			$config2 = $model->loadAccount("common");
			$ok = true;
			foreach($config as $key=>$item){
				if($config[$key] != $config2[$key]){
					$ok = false;
					break;
				}
			}
			if(!$ok){
				$this["use"]->setDefaultValue(true);
			}
		}
	}

	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$model = $this->presenter->context->services->system;

		if ($values["section"] == "common" || $values["use"]) {
			$model->saveAccount($values["name"], $values["password"], $values["section"]);
		}else{
			$config = $model->loadAccount("common");
			$model->saveAccount($config ["name"], $config ["password"], $values["section"]);
		}
	}

}
