<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\NavigationModule;

use Venne\ORM\Column;
use Nette\Utils\Html;

/**
 * @author Josef Kříž
 */
class NavigationForm extends \Venne\Developer\Form\EditForm {

	public function startup()
	{
		parent::startup();

		$data = $this->presenter->context->services->modules->getModules();

		$this->addGroup("Item");
		$this->addHidden("id");
		$this->addText("name", "Name");
		$this->addSelect("type", "Type", array("link" => "link", "url" => "url", "dir" => "dir"))->setDefaultValue("link");
		$this->addSelect("navigation_id", "Parent")->setPrompt("root");

		$this->addText("url", "URL");
		$this->addGroup("Link")->setOption('container', Html::el('fieldset')->id("linkto"));

		\DependentSelectBox\DependentSelectBox::$disableChilds = false;
		$this->addSelect("module", "Module")
				->setItems($data, false)
				->setDefaultValue("Pages");
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
		
		if($this->key){
			$this["navigation_id"]->setItems($this->presenter->context->services->navigation->getCurrentList($this->key));
		}else{
			$this["navigation_id"]->setItems($this->presenter->context->services->navigation->getCurrentList());
		}
		$this["navigation_id"]->setPrompt("root");
	}


	public function getValuesPresenter($form, $dependentSelectBoxName)
	{
		$module = $form["module"]->getValue();

		$presenters = array();
		$data = $this->presenter->context->services->modules->getPresenters($module);
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
		$data = $this->presenter->context->services->modules->getActions($module, $presenter);
		foreach ($data as $item) {
			$actions[$item] = $item;
		}

		return $actions;
	}


	public function getValuesParams($form, $dependentSelectBoxName)
	{
		$module = $form["module"]->getValue();
		$presenter = $form["presenter"]->getValue();

		if (!$presenter) {
			return array();
		}

		$params = array();
		$data = $this->presenter->context->services->modules->getParams($module, $presenter);
		foreach ($data as $item) {
			$params[$item] = $item;
		}

		return array("" => "") + $params;
	}


	public function load()
	{
		$entity = $this->presenter->context->services->navigation->getRepository()->find($this->key);
		$this["name"]->setValue($entity->name);
		$this["type"]->setValue($entity->type);
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
		if ($entity->parent) {
			$this["navigation_id"]->setValue($entity->parent->id);
		}
	}


	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$presenter = $this->getPresenter();
		$service = $presenter->getContext()->services->navigation;
		$em = $service->getEntityManager();

		if (!$this->key) {
			$entity = $service->create(array(), true);
			$em->persist($entity);
		} else {
			$entity = $this->presenter->context->services->navigation->getRepository()->find($this->key);
			foreach ($entity->keys as $key) {
				$em->remove($key);
			}
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
					$ent = new NavigationKeyEntity();
					$ent->key = $item;
					$ent->val = $values[$item];
					$ent->navigation = $entity;
					$em->persist($ent);
				}
			}
			for ($i = 0; $i < 4; $i++) {
				if ($values["param_$i"]) {
					$ent = new NavigationKeyEntity();
					$ent->key = $values["param_$i"];
					$ent->val = $values["value_$i"];
					$ent->navigation = $entity;
					$em->persist($ent);
				}
			}
		}
		$service->update($entity, $values, true);
		$entity->parent = $service->getRepository()->find($values["navigation_id"]);
		$entity->active = true;

		if (!$entity->order) {
			$entity->order = $service->getOrderValue((isset($entity->parent->id) && $entity->parent->id) ? $entity->parent->id : NULL);
		}

		$em->flush();
	}

}
