<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace DefaultModule;

/**
 * @author Josef Kříž
 */
class DefaultPresenter extends \Venne\CMS\Developer\Presenter\AdminPresenter
{
	
	public function renderDefault()
	{
		$this->setTitle("Venne:CMS");
		$this->setKeywords("Venne:CMS");
		$this->setDescription("Venne:CMS");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}
