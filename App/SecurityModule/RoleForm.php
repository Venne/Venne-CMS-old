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
class RoleForm extends \Venne\Forms\EntityForm {


	public function startup()
	{
		parent::startup();
		$this->addGroup("Role");
		$this->addText("name", "Name");
		$this->addSelect("parent", "Parent")
				->setItems($this->presenter->context->services->role->getList())
				->setPrompt("root");
	}


	public function load()
	{
		$this->setDefaults($this->key);
	}


	public function save()
	{
		$values = $this->getValues();
		
		if (!$this->key) {
			$this->key = $this->presenter->context->services->role->create($values);
		} else {
			$this->presenter->context->services->role->update($this->key, $values);
		}
		if($values["role_id"]){
			$this->key->parent = $this->presenter->context->services->role->getRepository()->find($values["role_id"]);
			$this->presenter->context->doctrineContainer->entityManager->flush();
		}
	}

}
