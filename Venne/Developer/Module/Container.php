<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Module;

use Venne;

/**
 * @author Josef Kříž
 * 
 * @property-read \Venne\Application\Container $context
 */
class Container extends \Nette\DI\Container implements IContainer {


	/**
	 * @param \Venne\Application\Container $context
	 * @param array $params
	 * @return Venne\Developer\Module\Container 
	 */
	public static function create(\Venne\Application\Container $context, $params = array())
	{
		$container = new self;
		$container->addService("context", $context);
		return $container;
	}

}