<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Forms;


/**
 * @author     Josef Kříž
 */
class DynamicEntityForm extends EntityForm
{
	
	protected $entity;
	
	protected $entityClass;
	
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $entityClass)
	{
		$presenter = $parent;
		
		$this->entityClass = $entityClass;
		
		$ref = new \Nette\Reflection\ClassType($entityClass);
		
		foreach($ref->getProperties() as $val){
			if($val->hasAnnotation("Form")){
				$an = $val->getAnnotation("Form");
				
				if(isset ($an["group"])){
					if($an["group"] == "current"){
						$this->setCurrentGroup();
					}else{
						$this->addGroup($an["group"]);
					}
				}
				
				isset ($an["label"]) ? $label = $an["label"] : $label = ucfirst($val->getName());
				
				if(!isset ($an["type"])){
					if($val->hasAnnotation("Column")){
						$an2 = $val->getAnnotation("Column");
						if($an2["type"] == "string"){
							$this->addText($val->getName(), $label);
						}
						else if($an2["type"] == "text") {
							$this->addTextArea($val->getName(), $label);
						}
					}else if($val->hasAnnotation("ManyToOne")){
						if($val->hasAnnotation("ManyToOne")){
							$ass = $val->getAnnotation("ManyToOne");
							$values = $presenter->getContext()->entityManager->getRepository("Venne\\CMS\\Modules\\" . $ass["targetEntity"])->fetchPairs($ass["inversedBy"], $an["value"]);
						}

						$this->addSelect($val->getName(), $label, $values);
					}
				}else{
					if($an["type"] == "text"){
						$this->addText($val->getName(), $label);
					}
					else if($an["type"] == "textarea"){
						$this->addTextArea($val->getName(), $label);
					}
					else if($an["type"] == "select"){

						if($val->hasAnnotation("ManyToOne")){
							$ass = $val->getAnnotation("ManyToOne");
							$values = $presenter->getContext()->entityManager->getRepository("Venne\\CMS\\Modules\\" . $ass["targetEntity"])->fetchPairs($ass["inversedBy"], $an["value"]);
						}

						$this->addSelect($val->getName(), $label, $values);
					}
				}
			}
		}
		
		$this->setCurrentGroup();
		parent::__construct($parent, $name);
	}
	
	public function save()
	{
		$em = $this->getPresenter()->getContext()->entityManager;
		
		if(!$this->entity){
			$this->entity = new $this->entityClass();
			$em->persist($this->entity);
		}
		$this->mapToEntity($this->entity);
		$em->flush();
	}
	
	public function mapToEntity($entity)
	{
		$ref = new \Nette\Reflection\ClassType($this->entityClass);
		foreach($this->getComponents() as $component){
			
			if($ref->hasProperty($component->getName())){
			
				$val = $ref->getProperty($component->getName());

				if($val->hasAnnotation("ManyToOne")){
					$ass = $val->getAnnotation("ManyToOne");

					$assEntity = $this->presenter->getContext()->entityManager->getRepository("Venne\\CMS\\Modules\\" . $ass["targetEntity"])->findOneBy(array($ass["inversedBy"] => $component->getValue()));

					$entity->{$component->getName()} = $assEntity;
				}else{

					$entity->{$component->getName()} = $component->getValue();

				}
			}
		}
	}
	
}
