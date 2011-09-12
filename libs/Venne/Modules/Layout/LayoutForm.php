<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Modules;

use Venne\ORM\Column;
use Nette\Utils\Html;

/**
 * @author Josef Kříž
 */
class LayoutForm extends \Venne\Developer\Form\EntityForm{
	
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
		$data = $this->getPresenter()->getContext()->cms->moduleManager->getFrontModules();
		foreach ($data as $item) {
			$modules[ucfirst($item)] = ucfirst($item);
		}
		
		$this->addGroup("Layout");
		
		$this->addSelect("layout", "Layout", $this->getPresenter()->getContext()->cms->moduleManager->getLayouts())->addRule(self::FILLED, "Enter layout");
		
		$this->addGroup("Position");
		
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
		$data = $this->getPresenter()->getContext()->cms->moduleManager->getPresenters($module);
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
		$data = $this->getPresenter()->getContext()->cms->moduleManager->getActions($module, $presenter);
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
		$data = $this->getPresenter()->getContext()->cms->moduleManager->getParams($module, $presenter);
		foreach ($data as $item) {
			$params[$item] = $item;
		}

		return array(""=>"")+$params;
	}
	
	public function load()
	{
		if($this->id){
			$model = $this->getPresenter()->getContext()->cms->layout->model;
			
			$values = $model->getLayout($this->id);
			$regex = explode(":",$values->regex);
			
			$this["module"]->setDefaultValue($regex[0]);
			$this["presenter"]->setDefaultValue($regex[1]);
			$this["action"]->setDefaultValue($regex[2]);
			
			$this["layout"]->setDefaultValue($values->layout);
			
			$i = 0;
			foreach($values->keys as $key){
				$this["param_$i"]->setValue($key->key);
				$this["value_$i"]->setValue($key->val);
				$i++;
			}
		}
	}


	public function save()
	{
		$values = $this->getValues();
		$model = $this->getPresenter()->getContext()->cms->layout->model;
		
		$params = array();
		for ($i = 0; $i < 4; $i++) {
			if($values["param_$i"]){
				$params[$values["param_$i"]] = $values["value_$i"];
			}
		}
		
		if(!$this->id){
			$model->createLayout($values["layout"], $values["module"], $values["presenter"], $values["action"], $params);
		}else{
			$model->updateLayout($this->id, $values["layout"], $values["module"], $values["presenter"], $values["action"], $params);
		}
	}

}
