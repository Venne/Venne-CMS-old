<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Module\Service;

/**
 * @author Josef Kříž
 */
interface IFrontService{
	
	/**
	 * @param array of Nette\Application\Routers\RouteList
	 */
	public function getRoute(\Nette\Application\Routers\RouteList $router, $values = array(), $prefix = "");

}

