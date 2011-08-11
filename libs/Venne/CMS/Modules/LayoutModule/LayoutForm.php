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
class LayoutForm extends \Venne\CMS\Developer\Form\EntityForm{
	
	protected $id;
	
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $id = NULL)
	{
		$this->id = $id;
		parent::__construct($parent, $name);
	}
	
	public function startup()
	{
		parent::startup();
		$this->addGroup("Layout");
		
		$this->addText("layout", "Layout")->addRule(self::FILLED, "Enter layout");
		
		$this->addGroup("Position");
		
		$this->addText("module", "Module");
		$this->addText("presenter", "Presenter");
		$this->addText("action", "Action");
		
		for ($i = 0; $i < 4; $i++) {
			$this->addGroup("Param ".($i+1))->setOption('container', Html::el('fieldset')
							->id("par$i")
							->class('collapsible'));
			$this->addText("param_$i", "Parameter");
			$this->addText("value_$i", "Value");
		}
		
	}
	
	public function load()
	{
		if($this->id){
			$model = $this->getPresenter()->getContext()->layout->model;
			
			$values = $model->getLayout($this->id);
			$regex = explode(":",$values->regex);
			
			$this["module"]->setValue($regex[0]);
			$this["presenter"]->setValue($regex[1]);
			$this["action"]->setValue($regex[2]);
			
			$this["layout"]->setValue($values->layout);
			
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
		$model = $this->getPresenter()->getContext()->layout->model;
		
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
