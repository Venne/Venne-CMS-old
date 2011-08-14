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
class UsersForm extends \Venne\CMS\Developer\Form\EntityForm {


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
		$this->addMultiSelect("roles", "Roles", $this->getPresenter()->getContext()->entityManager->getRepository($this->getPresenter()->getContext()->params["venneModulesNamespace"] . "Role")->fetchPairs("id", "name"));
	}


	public function setValuesFromEntity()
	{
		parent::setValuesFromEntity();
		$roles = array();
		foreach ($this->entity->getRoleEntities() as $role) {
			$roles[$role->id] = $role->id;
		}
		$this["roles"]->setValue($roles);
	}


	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$presenter = $this->getPresenter();
		$service = $presenter->getContext()->navigation;
		$em = $service->getEntityManager();

		if (!$this->entity) {
			$this->entity = new User;
			$em->persist($this->entity);
			$this->entity->salt = \Nette\Utils\Strings::random(8);
		} else {
			/* remove roles */
			foreach ($this->entity->getRoleEntities() as $role) {
				$this->entity->removeRole($role);
			}
			$em->flush();
		}

		$this->entity->name = $values["name"];
		$this->entity->email = $values["email"];

		/* password */
		if($values["password_new"]){
			$this->entity->password = md5($this->entity->salt . $values["password"]);
		}

		/* add roles */
		foreach ($values["roles"] as $role) {
			$this->entity->addRole($this->getPresenter()->getContext()->entityManager->getRepository($this->getPresenter()->getContext()->params["venneModulesNamespace"] . "Role")->find($role));
		}
		$em->flush();
	}

}
