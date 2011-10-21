<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\SecurityModule;

use Venne\ORM\Column;
use Nette\Utils\Html;

/**
 * @author Josef Kříž
 */
class UserForm extends \Venne\Developer\Form\EntityForm {

	/** @var UserEntity*/
	protected $key;

	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $key = NULL)
	{
		parent::__construct($parent, $name);
		$this->key = $key;
		if (!$this["_submit"]->isSubmittedBy() && $this->key) {
			$this->load();
		}
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
		$this["password_new"]->setDefaultValue(false);
		$this["name"]->setDefaultValue($this->key->name);
		$this["email"]->setDefaultValue($this->key->email);
		$this["roles"]->setDefaultValue($this->key->roleEntities->getKeys());
	}


	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$presenter = $this->getPresenter();

		try {
			if (!$this->key) {
				$this->key = $this->presenter->context->services->user->create($values, true);
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
			if ($values["password_new"]) {
				$this->key->password = md5($this->key->salt . $values["password"]);
			}

			/* add roles */
			foreach ($values["roles"] as $role) {
				$this->key->addRole($this->presenter->context->services->role->repository->find($role));
			}
		} catch (\SecurityModule\UserNameExistsException $e) {
			$this->presenter->flashMessage("Uživatelské jméno je již používáno", "warning");
			return false;
		} catch (\SecurityModule\UserEmailExistsException $e) {
			$this->presenter->flashMessage("Uživatelský e-mail je již používán", "warning");
			return false;
		}

		$this->presenter->context->doctrineContainer->entityManager->flush();
	}

}
