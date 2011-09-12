<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace SecurityModule\AdminModule;

/**
 * @author Josef Kříž
 * @resource AdminModule\SecurityModule\Permission
 */
class PermissionsPresenter extends BasePresenter {


	/** @persistent */
	public $role;


	public function startup()
	{
		parent::startup();

		if (!$this->role) {
			$this->role = "guest";
		}

		$this->addPath("Permissions", $this->link(":Security:Admin:Permissions:"));

		$role = $this->context->services->role->repository->findOneByName($this->role);


		$allowed = $this->context->services->permission->repository->findBy(array("role"=>$role->id));
		$this->template->allowed = array();
		foreach ($allowed as $item) {
			$this->template->allowed[$item->resource][$item->privilege] = $item;
		}

		$this->template->roles = $this->context->services->role->repository->findAll();
		$role = $this->context->services->role->repository->findOneByName($this->role);

		$this->template->permissions = $this->context->authorizator->getResources();
		$this->template->privileges = $this->context->authorizator->getPrivileges();
	}


	public function createComponentFormRole($name)
	{
		$form = new \Venne\Application\UI\Form($this, $name);
		$form->addGroup("Role");
		$form->addSelect("role", "Role", $this->context->services->role->repository->fetchPairs("name", "name"));
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
		$this->formRecursion($form, $this->template->permissions["root"]);
		$form->onSuccess[] = array($this, "handleSave");
		return $form;
	}


	public function formRecursion($form, $menu)
	{
		if ($menu) {
			foreach ($menu as $item) {
				if (isset($this->template->privileges[$item])) {
					foreach ($this->template->privileges[$item] as $item2) {
						$form->addSubmit("allow_" . str_replace("\\", "_", $item) . "_" . $item2, "Allow");
						$form->addSubmit("deny_" . str_replace("\\", "_", $item) . "_" . $item2, "Deny");
						$form->addSubmit("delete_" . str_replace("\\", "_", $item) . "_" . $item2, "Delete")->getControlPrototype()->class = "grey";
					}
				}
				$form->addSubmit("allow_" . str_replace("\\", "_", $item), "Allow");
				$form->addSubmit("deny_" . str_replace("\\", "_", $item), "Deny");
				$form->addSubmit("delete_" . str_replace("\\", "_", $item), "Delete")->getControlPrototype()->class = "grey";
				if (isset($this->template->permissions[$item])) {
					$this->formRecursion($form, $this->template->permissions[$item]);
				}
			}
		}
	}


	public function formSaveRecursion($form, $menu)
	{
		foreach ($menu as $key => $item) {
			if (isset($this->template->privileges[$item])) {
				foreach ($this->template->privileges[$item] as $item2) {
					if ($form["allow_" . str_replace("\\", "_", $item) . "_" . $item2]->isSubmittedBy()) {
						$data = array(
							"resource" => $item,
							"role" => $this->context->services->role->repository->findOneByName($this->role),
							"allow" => true,
							"privilege" => $item2
						);
						$entity = $this->context->services->permission->create($data);
						$this->flashMessage("Permission has been saved", "success");
						$this->redirect("this");
					}
					if ($form["deny_" . str_replace("\\", "_", $item) . "_" . $item2]->isSubmittedBy()) {
						$data = array(
							"resource" => $item,
							"role" => $this->context->services->role->repository->findOneByName($this->role),
							"allow" => false,
							"privilege" => $item2
						);
						$permission = $this->context->services->permission->create($data);
						$this->flashMessage("Permission has been saved", "success");
						$this->redirect("this");
					}
					if ($form["delete_" . str_replace("\\", "_", $item) . "_" . $item2]->isSubmittedBy()) {
						$item = $this->context->services->permission->repository->findOneBy(array("resource" => $item, "role" => $this->context->services->role->repository->findOneByName($this->role)->id, "privilege" => $item2));
						$permission = $this->context->services->permission->delete($item);

						$this->flashMessage("Permission has been deleted", "success");
						$this->redirect("this");
					}
				}
			}
			if ($form["allow_" . str_replace("\\", "_", $item)]->isSubmittedBy()) {
				$data = array(
					"resource" => $item,
					"role" => $this->context->services->role->repository->findOneByName($this->role),
					"allow" => true
				);
				$permission = $this->context->services->permission->create($data);
				$this->flashMessage("Permission has been saved", "success");
				$this->redirect("this");
			}
			if ($form["deny_" . str_replace("\\", "_", $item)]->isSubmittedBy()) {
				$data = array(
					"resource" => $item,
					"role" => $this->context->services->role->repository->findOneByName($this->role),
					"allow" => false
				);
				$permission = $this->context->services->permission->create($data);
				$this->flashMessage("Permission has been saved", "success");
				$this->redirect("this");
			}
			if ($form["delete_" . str_replace("\\", "_", $item)]->isSubmittedBy()) {
				$item = $this->context->services->permission->repository->findOneBy(array("resource" => $item, "role" => $this->context->services->role->repository->findOneByName($this->role)->id, "privilege" => NULL));
				$permission = $this->context->services->permission->delete($item);

				$this->flashMessage("Permission has been deleted", "success");
				$this->redirect("this");
			}
			if (isset($this->template->permissions[$item])) {
				$this->formSaveRecursion($form, $this->template->permissions[$item]);
			}
		}
	}


	public function handleSave()
	{
		$this->formSaveRecursion($this["form"], $this->template->permissions["root"]);
	}


	public function renderDefault()
	{
		$this["formRole"]["role"]->setValue($this->role);
		$this->template->role = $this->role;
	}

}
