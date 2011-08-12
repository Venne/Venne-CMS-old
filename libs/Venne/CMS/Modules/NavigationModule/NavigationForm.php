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
class NavigationForm extends \Venne\CMS\Developer\Form\EntityForm {


	protected $id;


	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $id = NULL)
	{
		$this->id = $id;
		parent::__construct($parent, $name);
	}


	public function startup()
	{
		parent::startup();

		$modules = array();
		$data = $this->getPresenter()->getContext()->moduleManager->getRouteModules();
		foreach ($data as $item) {
			$modules[ucfirst($item)] = ucfirst($item);
		}

		$this->addGroup("Item");
		$this->addHidden("id");
		$this->addText("name", "Name");
		$this->addSelect("type", "Type", array("link" => "link", "url" => "url", "dir" => "dir"))->setDefaultValue("link");
		$this->addSelect("navigation_id", "Parent")->setPrompt("root");

		$this->addText("url", "URL");
		$this->addGroup("Link")->setOption('container', Html::el('fieldset')->id("linkto"));

		\DependentSelectBox\DependentSelectBox::$disableChilds = false;
		$this->addSelect("module", "Module", $modules)->setDefaultValue("Pages");
		$this->addDependentSelectBox("presenter", "Presenter", $this["module"], array($this, "getValuesPresenter"))->setDefaultValue("Default");
		$this->addDependentSelectBox("action", "Action", $this["presenter"], array($this, "getValuesAction"))->setDefaultValue("default");

		for ($i = 0; $i < 4; $i++) {
			$this->addGroup("Param " . ($i + 1))->setOption('container', Html::el('fieldset')
							->id("par$i")
							->class('collapsible'));
			$this->addDependentSelectBox("param_$i", "Parameter", $this["presenter"], array($this, "getValuesParams"));
			$this->addText("value_$i", "Value");
		}

		if ($this->getPresenter()->isAjax()) {
			$this["presenter"]->addOnSubmitCallback(array($this->getPresenter(), "invalidateControl"), "form");
			$this["action"]->addOnSubmitCallback(array($this->getPresenter(), "invalidateControl"), "form");
		}
	}


	public function getValuesPresenter($form, $dependentSelectBoxName)
	{
		$module = $form["module"]->getValue();
		
		$presenters = array();
		$data = $this->getPresenter()->getContext()->moduleManager->getPresenters($module);
		foreach ($data as $item) {
			$presenters[ucfirst($item)] = ucfirst($item);
		}

		return $presenters;
	}


	public function getValuesAction($form, $dependentSelectBoxName)
	{
		$module = $form["module"]->getValue();
		$presenter = $form["presenter"]->getValue();

		$actions = array();
		$data = $this->getPresenter()->getContext()->moduleManager->getActions($module, $presenter);
		foreach ($data as $item) {
			$actions[$item] = $item;
		}

		return $actions;
	}


	public function getValuesParams($form, $dependentSelectBoxName)
	{
		$module = $form["module"]->getValue();
		$presenter = $form["presenter"]->getValue();

		if(!$presenter){
			return array();
		}
		
		$params = array();
		$data = $this->getPresenter()->getContext()->moduleManager->getParams($module, $presenter);
		foreach ($data as $item) {
			$params[$item] = $item;
		}

		return array(""=>"")+$params;
	}


	public function load()
	{
		if ($this->id) {
			$entity = $this->getPresenter()->getContext()->navigation->getRepository()->find($this->id);
			if ($entity->type == "url") {
				$this["url"]->setValue($entity->keys["url"]->val);
			}
			if ($entity->type == "link") {
				if (isset($entity->keys["module"]))
					$this["module"]->setValue($entity->keys["module"]->val);
				if (isset($entity->keys["presenter"]))
					$this["presenter"]->setDefaultValue($entity->keys["presenter"]->val);
				if (isset($entity->keys["action"]))
					$this["action"]->setDefaultValue($entity->keys["action"]->val);
				$i = 0;
				foreach ($entity->keys as $item) {
					if ($item->key == "presenter" || $item->key == "module" || $item->key == "action")
						continue;
					$this["param_$i"]->setValue($item->key);
					$this["value_$i"]->setValue($item->val);
					$i++;
				}
			}
			if ($entity->parent)
				$this["navigation_id"]->setValue($entity->parent->id);
		}
	}


	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$presenter = $this->getPresenter();
		$service = $presenter->getContext()->navigation;
		$em = $service->getEntityManager();

		if (!$this->entity) {
			$this->entity = new Navigation;
			$em->persist($this->entity);
		} else {
			foreach ($this->entity->keys as $key) {
				$em->remove($key);
			}
			//$databaseManager->flush();
		}

		if ($values["type"] == "url") {
			$ent = new NavigationKey();
			$ent->key = "url";
			$ent->val = $values["url"];
			$ent->navigation = $this->entity;
			$em->persist($ent);
		} else if ($values["type"] == "link") {
			$arr = array("module", "presenter", "action");
			foreach ($arr as $item) {
				if ($values[$item]) {
					$ent = new NavigationKey();
					$ent->key = $item;
					$ent->val = $values[$item];
					$ent->navigation = $this->entity;
					$em->persist($ent);
				}
			}
			for ($i = 0; $i < 4; $i++) {
				if ($values["param_$i"]) {
					$ent = new NavigationKey();
					$ent->key = $values["param_$i"];
					$ent->val = $values["value_$i"];
					$ent->navigation = $this->entity;
					$em->persist($ent);
				}
			}
		}
		$this->mapToEntity($this->entity);
		$this->entity->parent = $service->getRepository()->find($values["navigation_id"]);
		$this->entity->active = true;
		$this->entity->website = $presenter->getContext()->website->currentFront;

		if (!$this->entity->order) {
			$this->entity->order = $service->model->getOrderValue($this->entity->website->id, (isset($this->entity->parent->id) && $this->entity->parent->id) ? $this->entity->parent->id : NULL);
		}

		$em->flush();
	}

}
