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

		$this->getPresenter()->addJs("/js/Forms/Controls/tagInput.js");
		$this->getPresenter()->addCss("/css/Forms/Controls/tagInput.css");

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

		$this->getPresenter()->addJs("/js/jquery-ui-timepicker-addon.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/DateInput.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/DateInputSettings.js");
		$this->getPresenter()->addCss("/css/Forms/Controls/DateInput.css");

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

		$this->getPresenter()->addJs("/js/jquery-ui-timepicker-addon.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/DateInput.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/DateInputSettings.js");
		$this->getPresenter()->addCss("/css/Forms/Controls/DateInput.css");

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

		$this->getPresenter()->addJs("/js/jquery-ui-timepicker-addon.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/DateInput.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/DateInputSettings.js");
		$this->getPresenter()->addCss("/css/Forms/Controls/DateInput.css");

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

		$this->getPresenter()->addJs("/js/Forms/Controls/jquery.nette.dependentselectbox.js");

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
		$ret->getControlPrototype()->class[] = "control-editor";
		$ret->getControlPrototype()->venneBasePath[] = $this->presenter->template->basePath;

		$this->getPresenter()->addJs("/ckeditor/ckeditor.js");
		$this->getPresenter()->addJs("/ckeditor/adapters/jquery.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/Editor.js?basePath=" . $this->presenter->template->basePath);

		return $ret;
	}

}
