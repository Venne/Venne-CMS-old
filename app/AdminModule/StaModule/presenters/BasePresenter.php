<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace AdminModule\StaModule;

/**
 * @author Josef Kříž
 * @resource AdminModule\StaModule
 */
abstract class BasePresenter extends \Venne\Developer\Presenter\AdminPresenter
{
	
	/** @persistent */
	public $type;
	
	public function startup()
	{
		parent::startup();
		$this->addPath("Stawebniny", $this->link(":Admin:Sta:Default:"));
	}
	
	public function beforeRender()
	{
		parent::beforeRender();
		$this->setTitle("Venne:CMS | Stawebniny administration");
		$this->setKeywords("stawebniny administration");
		$this->setDescription("Stawebniny administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}
