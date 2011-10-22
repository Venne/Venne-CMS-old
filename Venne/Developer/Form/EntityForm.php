<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Form;

/**
 * @author     Josef Kříž
 */
class EntityForm extends EditForm {


	/**
	 * Application form constructor.
	 */
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, \Venne\Developer\Doctrine\BaseEntity $key = NULL)
	{
		parent::__construct($parent, $name, $key);
	}


	/**
	 * @param \Nella\Models\IEntity
	 * @param bool
	 * @return EntityForm
	 */
	public function setDefaults($entity, $erase = FALSE)
	{
		if ($entity instanceof \Venne\Developer\Doctrine\BaseEntity) {
			$supportedComponents = array(
				'\Nette\Forms\Controls\TextBase',
				'\Nette\Forms\Controls\RadioList',
				'\Nette\Forms\Controls\Checkbox',
				'\Nette\Forms\Controls\Selectbox',
				'\Venne\Forms\Controls\DateInput',
			);
			$arr = array();
			foreach ($this->getComponents() as $component) {
				foreach ($supportedComponents as $class) {
					if (!$component instanceof $class) {
						continue;
					}
				}
				$name = $component->getName();

				$value = $entity->$name;
				if ($component instanceof \Nette\Forms\Controls\Selectbox) {
					$arr[$name] = $value ? (is_string($value) ? $value : $value->id) : NULL;
				} else {
					$arr[$name] = $value;
				}
				if($arr[$name] === NULL){
					unset($arr[$name]);
				}
			}
		} else {
			$arr = $entity;
		}
		return parent::setDefaults($arr, $erase);
	}
	
	public function load()
	{
		$this->setDefaults($this->key);
	}

}
