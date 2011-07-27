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
class EntityForm extends Form {


	protected $entity;
	protected $flash;

	protected $supportedComponents = array(
			'Nette\Forms\Controls\TextBase',
			'Nette\Forms\Controls\RadioList',
			'Nette\Forms\Controls\Checkbox',
			'Nette\Forms\Controls\Selectbox',
		);

	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);
		$this->onSuccess[] = callback($this, 'onSubmitForm');
	}


	public function setEntity($entity)
	{
		$this->entity = $entity;
		if (!$this->isSubmitted()) {
			$this->setValuesFromEntity();
		}
	}
	
	public function getEntity()
	{
		return $this->entity;
	}


	public function setFlashMessage($value)
	{
		$this->flash = $value;
	}


	public function setValuesFromEntity()
	{
		foreach ($this->getComponents() as $component) {
			$ok = false;
			foreach ($this->supportedComponents as $class) {
				if ($component instanceof $class) {
					$ok = true;
					break;
				}
			}
			if($ok){
				$component->setValue($this->entity->{$component->getName()});
			}
		}
	}


	public function mapToEntity($entity)
	{
		foreach ($this->getComponents() as $component) {
			$ok = false;
			foreach ($this->supportedComponents as $class) {
				if ($component instanceof $class) {
					$ok = true;
					break;
				}
			}
			if($ok){
				$entity->{$component->getName()} = $component->getValue();
			}
		}
	}


	public function onSubmitForm()
	{
		if(!$this->isValid()){
			return ;
		}
		
		$this->save();
		if ($this->flash)
			$this->getPresenter()->flashMessage($this->flash, "success");
		if ($this->successLink)
			$this->presenter->redirect($this->successLink);
	}


	public function save()
	{
		
	}

}
