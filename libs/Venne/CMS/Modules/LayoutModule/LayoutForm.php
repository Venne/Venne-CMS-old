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
		$this->addText("regex", "Regex")->addRule(self::FILLED, "Enter regex");
		$this->addText("layout", "Layout")->addRule(self::FILLED, "Enter layout");
	}
	
	public function load()
	{
		if($this->id){
			$entity = $this->getPresenter()->getContext()->layout->getRepository()->find($this->id);
			$this["regex"]->setValue($entity->regex);
			$this["layout"]->setValue($entity->layout);
		}
	}


	public function save()
	{
		$values = $this->getValues();
		$presenter = $this->getPresenter();
		$service = $presenter->getContext()->navigation;
		$em = $service->getEntityManager();
		
		if(!$this->id){
			$this->entity = new Layout;
			$em->persist($this->entity);
		}else{
			$this->entity = $this->getPresenter()->getContext()->layout->getRepository()->find($this->id);
		}
		
		$this->entity->layout = $values["layout"];
		$this->entity->regex = $values["regex"];
		
		$this->entity->website = $presenter->getContext()->website->currentFront;
		$em->flush();
	}

}
