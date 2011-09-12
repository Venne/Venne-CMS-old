<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\SecurityModule;

use Venne\ORM\Column;
use Nette\Utils\Html;

/**
 * @author Josef Kříž
 */
class UserForm extends \Venne\Developer\Form\EntityForm {

	
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $key = NULL)
	{
		$this->key = $key;
		parent::__construct($parent, $name);
	}

	public function startup()
	{
		parent::startup();
		$this->addGroup("User");
		$this->addText("name", "Name");
		$this->addCheckbox("password_new", "Set password")->setDefaultValue(true);
		$this->addPassword("password", "Password")
				->setOption("description", "minimal length is 5 char")
				->addConditionOn($this['password_new'], \Nette\Forms\Form::FILLED)
					->addRule(\Nette\Forms\Form::FILLED, 'Enter password')
					->addRule(\Nette\Forms\Form::MIN_LENGTH, 'Password is short', 5);
		$this->addPassword("password_confirm", "Confirm password")
				->addRule(\Nette\Forms\Form::EQUAL, 'Invalid re password', $this['password']);

		$this->addGroup("Next informations");
		$this->addText("email", "E-mail")->addRule(\Nette\Forms\Form::EMAIL, "Enter email");
		$this->addMultiSelect("roles", "Roles", $this->presenter->context->services->role->getRepository()->fetchPairs("id", "name"));
	}


	public function load()
	{
		$this->setDefaults($this->key);
	}


	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$presenter = $this->getPresenter();

		if (!$this->key) {
			$this->key = $this->presenter->context->services->user->create(array(), true);
			$this->key->salt = \Nette\Utils\Strings::random(8);
		} else {
			/* remove roles */
			foreach ($this->key->getRoleEntities() as $role) {
				$this->key->removeRole($role);
			}
			$this->presenter->context->doctrineContainer->entityManager->flush();
		}

		$this->key->name = $values["name"];
		$this->key->email = $values["email"];

		/* password */
		if($values["password_new"]){
			$this->key->password = md5($this->key->salt . $values["password"]);
		}

		/* add roles */
		foreach ($values["roles"] as $role) {
			$this->key->addRole($this->presenter->context->services->role->repository->find($role));
		}
		$this->presenter->context->doctrineContainer->entityManager->flush();
	}

}
