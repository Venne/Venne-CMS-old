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
 * @allowed(administration-security-roles)
 */
class RolesPresenter extends BasePresenter {


	/** @persistent */
	public $id;


	public function startup()
	{
		parent::startup();
		$this->getNavigation()->addPath("Roles", $this->link(":Security:Roles:"));

		$this->template->items = $this->getContext()->entityManager->getRepository(VENNE_MODULES_NAMESPACE . "Role")->findBy(array("parent" => NULL));
	}


	/**
	 * @allowed(administration-security-roles-edit)
	 */
	public function actionCreate()
	{
		$this->getNavigation()->addPath("new item", $this->link(":Security:Roles:create"));
	}


	/**
	 * @allowed(administration-security-roles-edit)
	 */
	public function actionEdit()
	{
		$this->getNavigation()->addPath("edit" . " (" . $this->id . ")", $this->link(":Security:Roles:edit"));
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
				$this->getEntityManager()->remove($this->getEntityManager()->getRepository(VENNE_MODULES_NAMESPACE . "Role")->find($item->id));
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


	/**
	 * @allowed(administration-security-roles-edit)
	 */
	public function handleSave()
	{
		$this->formSaveRecursion($this["form"], $this->template->items);
	}


	/**
	 * @allowed(administration-security-roles-edit)
	 */
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
		$this->getEntityManager()->getRepository(VENNE_MODULES_NAMESPACE . "Role")->setStructure($data);
		$this->flashMessage("Structure has been saved.", "success");
		$this->redirect("this");
	}


	public function createComponentFormRole($name)
	{
		$form = new \Venne\CMS\Modules\RoleForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Role has been saved");
		$form->addSubmit("submit", "Create");
		$form["role_id"]->setItems($this->getEntityManager()->getRepository(VENNE_MODULES_NAMESPACE . "Role")->getList());
		$form["role_id"]->setPrompt("root");
		return $form;
	}


	public function createComponentFormRoleEdit($name)
	{
		$form = new \Venne\CMS\Modules\RoleForm($this, $name);
		$form->setEntity($this->getEntityManager()->getRepository(VENNE_MODULES_NAMESPACE . "Role")->find($this->getParam("id")));
		$form->setSuccessLink("default");
		$form->setFlashMessage("Role has been updated");
		$form->addSubmit("submit", "Update");
		$form["role_id"]->setItems($this->getEntityManager()->getRepository(VENNE_MODULES_NAMESPACE . "Role")->getList());
		$form["role_id"]->setPrompt("root");
		return $form;
	}

}
