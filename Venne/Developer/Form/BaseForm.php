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
class BaseForm extends \Venne\Application\UI\Form {


	protected $successLink;
	protected $successLinkParams;
	protected $flash;
	protected $flashStatus;


	/**
	 * Application form constructor.
	 */
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);
		$this->startup();
		$this->setCurrentGroup();
		$this->flashStatus = $this->getPresenter()->getContext()->params['flashes']['success'];
	}

	public function setSuccessLink($link, $params = NULL)
	{
		$this->successLink = $link;
		$this->successLinkParams = (array)$params;
	}


	public function setFlashMessage($value, $status = NULL)
	{
		if ($status) {
			$this->flashStatus = $status;
		}
		$this->flash = $value;
	}


	public function startup()
	{
		
	}


	public function onSubmitForm()
	{
		if (!$this->isValid()) {
			return;
		}

		if ($this->save() === NULL) {
			if ($this->flash)
				$this->getPresenter()->flashMessage($this->flash, $this->flashStatus);
			if ($this->successLink && !$this->presenter->isAjax()){
				$this->presenter->redirect($this->successLink, $this->successLinkParams);
			}
		}
	}


	public function save()
	{
		
	}


	/* --------------- new controls ------------ */


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
	public function addDynamic($name, $factory , $createDefault = 0)
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

}
