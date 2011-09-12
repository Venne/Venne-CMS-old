<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace AdminModule\SystemModule;

/**
 * @author Josef Kříž
 */
class BasePresenter extends \Venne\Developer\Presenter\AdminPresenter
{
	
	/** @persistent */
	public $mode = "common";
	
	public function startup()
	{
		parent::startup();
		$this->addPath("System", $this->link(":Admin:System:Default:"));
	}
	
	public function createComponentFormMode($name)
	{
		$form = new \Venne\Application\UI\Form($this, $name);
		$form->addGroup();
		$form->addSelect("mode", "Mode", array("common"=>"common", "development"=>"development", "production"=>"production", "console"=>"console"))->setDefaultValue($this->mode);
		$form->addSubmit("submit", "Select");
		$form->onSuccess[] = callback($this, "handleSelect");
		return $form;
	}

	public function handleSelect($form)
	{
		$this->mode = $form["mode"]->getValue();
		$this->redirect("this");
	}

	public function beforeRender()
	{
		parent::beforeRender();
		$this->setTitle("Venne:CMS | System administration");
		$this->setKeywords("system administration");
		$this->setDescription("System administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}
