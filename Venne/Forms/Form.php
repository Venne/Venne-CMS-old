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
class Form extends \Venne\Application\UI\Form {

	/**
	 * Application form constructor.
	 */
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);
		$this->getElementPrototype()->class[] = "venne-form";
	}

	/**
	 * Adds naming container to the form.
	 * @param  string  name
	 * @return Container
	 */
	public function addContainer($name)
	{
		$control = new \Venne\Forms\Container($this->getPresenter()->getContext());
		$control->currentGroup = $this->currentGroup;
		return $this[$name] = $control;
	}


	/**
	 * Adds naming container to the form.
	 * @param  string  name
	 * @return Container
	 */
	public function addDynamic($name, $factory, $createDefault = 0)
	{
		return $this[$name] = new \Venne\Forms\Containers\Replicator($factory, $createDefault);
	}


	/**
	 * @param string $name
	 * @param string $label
	 * @param array $suggest
	 * @return \Venne\Forms\Controls\TagInput provides fluent interface
	 */
	public function addTag($name, $label = NULL)
	{
		$this[$name] = new \Venne\Forms\Controls\TagInput($label);
		$this[$name]->setRenderName('tagInputSuggest' . ucfirst($name));
		$this->getPresenter()->addJs("/js/Forms/Controls/tag.js");
		return $this[$name];
	}


	/**
	 * @param string $label label
	 * @param int $cols šířka elementu input
	 * @param int $maxLength parametr maximální počet znaků
	 * @return \Venne\Forms\Controls\DateInput
	 */
	public function addDateTime($name, $label = NULL)
	{
		$this[$name] = new \Venne\Forms\Controls\DateInput($label, \Venne\Forms\Controls\DateInput::TYPE_DATETIME);
		$this->getPresenter()->addJs("/js/Forms/Controls/date.js");
		return $this[$name];
	}


	/**
	 * @param string $label label
	 * @param int $cols šířka elementu input
	 * @param int $maxLength parametr maximální počet znaků
	 * @return \Venne\Forms\Controls\DateInput
	 */
	public function addDate($name, $label = NULL)
	{
		$this[$name] = new \Venne\Forms\Controls\DateInput($label, \Venne\Forms\Controls\DateInput::TYPE_DATE);
		$this->getPresenter()->addJs("/js/Forms/Controls/date.js");
		return $this[$name];
	}


	/**
	 * @param string $label label
	 * @param int $cols šířka elementu input
	 * @param int $maxLength parametr maximální počet znaků
	 * @return \Venne\Forms\Controls\DateInput
	 */
	public function addTime($name, $label = NULL)
	{
		$this[$name] = new \Venne\Forms\Controls\DateInput($label, \Venne\Forms\Controls\DateInput::TYPE_TIME);
		$this->getPresenter()->addJs("/js/Forms/Controls/date.js");
		return $this[$name];
	}


	/**
	 * @param string $label label
	 * @param int $cols šířka elementu input
	 * @param int $maxLength parametr maximální počet znaků
	 * @return \Venne\Forms\Controls\DateInput
	 */
	public function addDependentSelectBox($name, $label, $parents, $dataCallback)
	{
		$this[$name] = new \DependentSelectBox\DependentSelectBox($label, $parents, $dataCallback);
		$this->getPresenter()->addJs("/js/Forms/Controls/dependentSelectBox.js");
		return $this[$name];
	}


	/**
	 * Adds editor input control to the form.
	 * @param  string  control name
	 * @param  string  label
	 * @param  int  width of the control
	 * @param  int  height of the control in text lines
	 * @return Nette\Forms\Controls\TextArea
	 */
	public function addEditor($name, $label = NULL, $cols = 40, $rows = 80)
	{
		$ret = parent::addTextArea($name, $label, $cols, $rows);
		$ret->getControlPrototype()->data('venne-editor', true);
		$this->getPresenter()->addJs("/js/Forms/Controls/editor.js");
		return $ret;
	}

}
