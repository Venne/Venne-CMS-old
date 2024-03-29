<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\SecurityModule\AdminModule;

/**
 * @author Josef Kříž
 * @resource AdminModule\SecurityModule\Roles
 */
class RolesPresenter extends BasePresenter {


	/** @persistent */
	public $id;


	public function startup()
	{
		parent::startup();
		$this->addPath("Roles", $this->link(":Security:Admin:Roles:"));

		$this->template->items = $this->context->services->role->getRepository()->findBy(array("parent" => NULL));
	}


	public function actionCreate()
	{
		$this->addPath("new item", $this->link(":Security:Admin:Roles:create"));
	}


	public function actionEdit()
	{
		$this->addPath("edit" . " (" . $this->id . ")", $this->link(":Security:Admin:Roles:edit"));
	}


	public function createComponentForm($name)
	{
		$form = new \Venne\Application\UI\Form($this, $name);
		$this->formRecursion($form, $this->template->items);
		$form->onSuccess[] = array($this, "handleSave");
		return $form;
	}


	public function createComponentFormSort($name)
	{
		$form = new \Venne\Application\UI\Form($this, $name);
		$form->addHidden("hash");
		$form->addSubmit("Save", "Save")->onClick[] = array($this, "handleSortSave");
		return $form;
	}


	public function formRecursion($form, $menu)
	{
		if ($menu) {
			foreach ($menu as $item) {
				$form->addSubmit("settings_" . $item->id, "Settings");
				$form->addSubmit("delete_" . $item->id, "Delete")->getControlPrototype()->class = "grey";
				if ($item->childrens)
					$this->formRecursion($form, $item->childrens);
			}
		}
	}


	public function formSaveRecursion($form, $menu)
	{
		foreach ($menu as $key => $item) {
			if ($form["delete_" . $item->id]->isSubmittedBy()) {
				$this->getEntityManager()->remove($this->context->services->role->getRepository()->find($item->id));
				$this->getEntityManager()->flush();
				$this->flashMessage("Role has been deleted", "success");
				$this->redirect("this");
			}
			if ($form["settings_" . $item->id]->isSubmittedBy()) {
				$this->redirect("edit", array("id" => $item->id));
			}

			if ($item->childrens)
				$this->formSaveRecursion($form, $item->childrens);
		}
	}


	public function handleSave()
	{
		$this->formSaveRecursion($this["form"], $this->template->items);
	}


	public function handleSortSave()
	{
		$data = array();
		$val = $this["formSort"]->getValues();
		$hash = explode("&", $val["hash"]);
		foreach ($hash as $item) {
			$item = explode("=", $item);
			$depend = $item[1];
			if ($depend == "root")
				$depend = Null;
			$id = \substr($item[0], 5, -1);
			if (!isset($data[$depend]))
				$data[$depend] = array();
			$order = count($data[$depend]) + 1;
			$data[$depend][] = array("id" => $id, "order" => $order, "role_id" => $depend);
		}
		$this->context->services->role->setStructure($data);
		$this->flashMessage("Structure has been saved.", "success");
		$this->redirect("this");
	}


	public function createComponentFormRole($name)
	{
		$form = new \App\SecurityModule\RoleForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Role has been saved");
		$form->setSubmitLabel("Create");
		return $form;
	}


	public function createComponentFormRoleEdit($name)
	{
		$form = new \App\SecurityModule\RoleForm($this, $name, $this->context->services->role->getRepository()->find($this->getParam("id")));
		$form->setSuccessLink("default");
		$form->setFlashMessage("Role has been updated");
		$form->setSubmitLabel("Update");
		return $form;
	}

}
