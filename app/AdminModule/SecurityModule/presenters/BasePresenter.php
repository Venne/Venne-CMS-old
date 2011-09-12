<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace AdminModule\SecurityModule;

/**
 * @author Josef Kříž
 * @resource AdminModule\SecurityModule
 */
class BasePresenter extends \Venne\Developer\Presenter\AdminPresenter
{
	
	public function startup()
	{
		parent::startup();
		$this->addPath("Security", $this->link(":Admin:Security:Default:"));
	}
	
	public function beforeRender()
	{
		parent::beforeRender();
		$this->setTitle("Venne:CMS | Security administration");
		$this->setKeywords("security administration");
		$this->setDescription("Security administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}
