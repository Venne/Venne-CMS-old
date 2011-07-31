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
class Form extends \Venne\Application\UI\Form
{
	
	protected $successLink;
	
	/**
	 * Application form constructor.
	 */
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);
		$this->startup();
		$this->setCurrentGroup();
		//$this->setTranslator($this->getPresenter()->getContext()->translator);
	}	
		
	public function setSuccessLink($link)
	{
		$this->successLink = $link;
	}


	public function startup()
	{
		
	}
	
	/**
	 * Adds naming container to the form.
	 * @param  string  name
	 * @return Container
	 */
	public function addContainer($name)
	{
		$control = new Container($this->getPresenter()->getContext());
		$control->currentGroup = $this->currentGroup;
		return $this[$name] = $control;
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
		
		$this->getPresenter()->addJs("/js/jquery-1.6.min.js");
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
		
		$this->getPresenter()->addJs("/js/jquery-1.6.min.js");
		$this->getPresenter()->addJs("/js/jquery-ui-timepicker-addon.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/DateInput.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/DateInputSettings.js");
		$this->getPresenter()->addCss("/css/jquery-ui-1.8.12.custom.css");
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
		
		$this->getPresenter()->addJs("/js/jquery-1.6.min.js");
		$this->getPresenter()->addJs("/js/jquery-ui-timepicker-addon.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/DateInput.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/DateInputSettings.js");
		$this->getPresenter()->addCss("/css/jquery-ui-1.8.12.custom.css");
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
		
		$this->getPresenter()->addJs("/js/jquery-1.6.min.js");
		$this->getPresenter()->addJs("/js/jquery-ui-timepicker-addon.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/DateInput.js");
		$this->getPresenter()->addJs("/js/Forms/Controls/DateInputSettings.js");
		$this->getPresenter()->addCss("/css/jquery-ui-1.8.12.custom.css");
		$this->getPresenter()->addCss("/css/Forms/Controls/DateInput.css");
		
		return $this[$name];
	}
	
}
