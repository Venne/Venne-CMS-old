<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace SecurityModule;

/**
 * @author Josef Kříž
 * @allowed(administration-security-permissions)
 */
class PermissionsPresenter extends BasePresenter {


	/** @persistent */
	public $role;


	public function startup()
	{
		parent::startup();
		$this->addPath("Permissions", $this->link(":Security:Permissions:"));

		$this->template->items = $this->getContext()->entityManager->getRepository(VENNE_MODULES_NAMESPACE . "Resource")->findBy(array("parent" => NULL));
		$this->template->roles = $this->getContext()->entityManager->getRepository(VENNE_MODULES_NAMESPACE . "Role")->findAll();
		if (!$this->role) {
			$this->role = "guest";
		}
		$role = $this->getContext()->entityManager->getRepository(VENNE_MODULES_NAMESPACE . "Role")->findOneByName($this->role);

		$this->template->permissions = array();
		$permissions = $this->getContext()->entityManager->getRepository(VENNE_MODULES_NAMESPACE . "Permission")->findByRole($role->id);
		foreach ($permissions as $permission) {
			$this->template->permissions[$permission->resource->name] = $permission;
		}
	}


	public function createComponentFormRole($name)
	{
		$form = new \Venne\Application\UI\Form($this, $name);
		$form->addGroup("Role");
		$form->addSelect("role", "Role", $this->getContext()->entityManager->getRepository(VENNE_MODULES_NAMESPACE . "Role")->fetchPairs("name", "name"));
		$form->addSubmit("submit", "Select");
		$form->onSuccess[] = array($this, "handleSaveRole");
		return $form;
	}


	public function handleSaveRole($form)
	{
		$this->role = $form["role"]->getValue();
		$this->redirect("this");
	}


	public function createComponentForm($name)
	{
		$form = new \Venne\Application\UI\Form($this, $name);
		$this->formRecursion($form, $this->template->items);
		$form->onSuccess[] = array($this, "handleSave");
		return $form;
	}


	public function formRecursion($form, $menu)
	{
		if ($menu) {
			foreach ($menu as $item) {
				$form->addSubmit("allow_" . $item->id, "Allow");
				$form->addSubmit("delete_" . $item->id, "Delete")->getControlPrototype()->class = "grey";
				if ($item->childrens) {
					$this->formRecursion($form, $item->childrens);
				}
			}
		}
	}


	public function formSaveRecursion($form, $menu)
	{
		foreach ($menu as $key => $item) {
			if ($form["allow_" . $item->id]->isSubmittedBy()) {
				$permission = new \Venne\CMS\Modules\Permission;
				$permission->resource = $this->getEntityManager()->getRepository(VENNE_MODULES_NAMESPACE . "Resource")->find($item->id);
				$permission->role = $this->getEntityManager()->getRepository(VENNE_MODULES_NAMESPACE . "Role")->findOneByName($this->role);
				$permission->allow = true;

				$this->getEntityManager()->persist($permission);
				$this->getEntityManager()->flush();
				$this->flashMessage("Permission has been saved", "success");
				$this->redirect("this");
			}
			if ($form["delete_" . $item->id]->isSubmittedBy()) {
				$item2 = $this->getEntityManager()->getRepository(VENNE_MODULES_NAMESPACE . "Permission")->findOneByResource($item->id);
				$this->getEntityManager()->remove($item2);
				$this->getEntityManager()->flush();
				$this->flashMessage("Permission has been deleted", "success");
				$this->redirect("this");
			}
			if ($item->childrens) {
				$this->formSaveRecursion($form, $item->childrens);
			}
		}
	}


	public function handleSave()
	{
		$this->formSaveRecursion($this["form"], $this->template->items);
	}


	public function renderDefault()
	{
		$this["formRole"]["role"]->setValue($this->role);
		$this->template->role = $this->role;
	}

}
