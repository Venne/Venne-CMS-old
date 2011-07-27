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
class NavigationForm extends \Venne\Forms\EntityForm{
	
	public function startup()
	{
		parent::startup();
		$this->addGroup("Item");
		$this->addHidden("id");
		$this->addText("name", "Name");
		$this->addSelect("type", "Type", array("url"=>"url","link"=>"link","dir"=>"dir"));
		$this->addSelect("navigation_id", "Parent")->setPrompt("root");

		$this->addText("url", "URL");

		$this->addGroup("Link")->setOption('container', Html::el('fieldset')->id("linkto"));
		$this->addText("module","Module");
		$this->addText("presenter","Presenter");
		$this->addText("action","Action");

		for ($i = 0; $i < 4; $i++) {
			$this->addGroup("Param ".($i+1))->setOption('container', Html::el('fieldset')
							->id("par$i")
							->class('collapsible'));
			$this->addText("param_$i", "Parameter");
			$this->addText("value_$i", "Value");
		}

		$this->setCurrentGroup();
		$this->addSubmit("save", "Save");
	}
	
	public function setValuesFromEntity()
	{
		parent::setValuesFromEntity();
		if($this->entity->type == "url"){
			$this["url"]->setValue($this->entity->keys["url"]->val);
		}
		if($this->entity->type == "link"){
			if(isset ($this->entity->keys["module"])) $this["module"]->setValue($this->entity->keys["module"]->val);
			if(isset ($this->entity->keys["presenter"])) $this["presenter"]->setValue($this->entity->keys["presenter"]->val);
			if(isset ($this->entity->keys["action"])) $this["action"]->setValue($this->entity->keys["action"]->val);
			$i = 0;
			foreach($this->entity->keys as $item){
				if($item->key == "presenter" || $item->key == "module" || $item->key == "action") continue;
				$this["param_$i"]->setValue($item->key);
				$this["value_$i"]->setValue($item->val);
				$i++;
			}
		}
		if($this->entity->parent) $this["navigation_id"]->setValue($this->entity->parent->id);
	}


	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$presenter = $this->getPresenter();
		$service = $presenter->getContext()->navigation;
		$em = $service->getEntityManager();
		
		if(!$this->entity){
			$this->entity = new Navigation;
			$em->persist($this->entity);
		}else{
			foreach($this->entity->keys as $key){
				$em->remove($key);
			}
			//$databaseManager->flush();
		}
		
		if($values["type"] == "url"){
			$ent = new NavigationKey();
			$ent->key = "url";
			$ent->val = $values["url"];
			$ent->navigation = $this->entity;
			$em->persist($ent);
		}
		else if($values["type"] == "link"){
			$arr = array("module", "presenter", "action");
			foreach($arr as $item){
				if($values[$item]){
					$ent = new NavigationKey();
					$ent->key = $item;
					$ent->val = $values[$item];
					$ent->navigation = $this->entity;
					$em->persist($ent);
				}
			}
			for ($i = 0; $i < 4; $i++) {
				if($values["param_$i"]){
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
		$em->flush();
	}

}
