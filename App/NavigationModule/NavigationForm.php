<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\NavigationModule;

use Venne\ORM\Column;
use Nette\Utils\Html;

/**
 * @author Josef Kříž
 */
class NavigationForm extends \Venne\Forms\EditForm {

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
			$this["navigation_id"]->setItems($this->presenter->context->services->navigation->getCurrentList($this->key->id));
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
		$this["name"]->setValue($this->key->name);
		$this["type"]->setValue($this->key->type);
		if ($this->key->type == "url") {
			$this["url"]->setValue($this->key->keys["url"]->val);
		}
		if ($this->key->type == "link") {
			if (isset($this->key->keys["module"]))
				$this["module"]->setValue($this->key->keys["module"]->val);
			if (isset($this->key->keys["presenter"]))
				$this["presenter"]->setDefaultValue($this->key->keys["presenter"]->val);
			if (isset($this->key->keys["action"]))
				$this["action"]->setDefaultValue($this->key->keys["action"]->val);
			$i = 0;
			foreach ($this->key->keys as $item) {
				if ($item->key == "presenter" || $item->key == "module" || $item->key == "action")
					continue;
				$this["param_$i"]->setValue($item->key);
				$this["value_$i"]->setValue($item->val);
				$i++;
			}
		}
		if ($this->key->parent) {
			$this["navigation_id"]->setValue($this->key->parent->id);
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
			$this->key = $service->create(array(), true);
			$em->persist($this->key);
		} else {
			$this->key->keys->clear();
		}

		if ($values["type"] == "url") {
			$ent = new NavigationKeyEntity();
			$ent->key = "url";
			$ent->val = $values["url"];
			$ent->navigation = $this->key;
			$em->persist($ent);
		} else if ($values["type"] == "link") {
			$arr = array("module", "presenter", "action");
			foreach ($arr as $item) {
				if ($values[$item]) {
					$ent = new NavigationKeyEntity();
					$ent->key = $item;
					$ent->val = $values[$item];
					$ent->navigation = $this->key;
					$em->persist($ent);
				}
			}
			for ($i = 0; $i < 4; $i++) {
				if ($values["param_$i"]) {
					$ent = new NavigationKeyEntity();
					$ent->key = $values["param_$i"];
					$ent->val = $values["value_$i"];
					$ent->navigation = $this->key;
					$em->persist($ent);
				}
			}
		}
		$service->update($this->key, $values, true);
		$this->key->parent = $service->getRepository()->find($values["navigation_id"]);
		$this->key->active = true;

		if (!$this->key->order) {
			$this->key->order = $service->getOrderValue((isset($this->key->parent->id) && $this->key->parent->id) ? $this->key->parent->id : NULL);
		}

		$em->flush();
	}

}
