<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace ModulesModule;

/**
 * @author Josef Kříž
 */
class BasePresenter extends \Venne\CMS\Developer\Presenter\AdminPresenter
{
	
	public function startup()
	{
		parent::startup();
		$this->addPath("Modules", $this->link(":Modules:Default:"));
	}
	
	public function beforeRender()
	{
		parent::beforeRender();
		$this->setTitle("Venne:CMS | Modules administration");
		$this->setKeywords("modules administration");
		$this->setDescription("Modules administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}
