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
class RoleForm extends \Venne\CMS\Developer\Form\EntityForm {


	public function startup()
	{
		parent::startup();
		$this->addGroup("Role");
		$this->addText("name", "Name");
		$this->addSelect("role_id", "Parent")->setPrompt("root");
	}


	public function setValuesFromEntity()
	{
		parent::setValuesFromEntity();
		if($this->entity->parent) $this["role_id"]->setValue($this->entity->parent->id);
	}


	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$presenter = $this->getPresenter();
		$em = $presenter->getContext()->entityManager;

		if (!$this->entity) {
			$this->entity = new Role;
			$em->persist($this->entity);
		} else {

		}

		$this->entity->name = $values["name"];
		$this->entity->parent = $em->getRepository(VENNE_MODULES_NAMESPACE . "Role")->find($values["role_id"]);
		$em->flush();
	}

}
