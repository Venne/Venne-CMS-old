<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Developer;

/**
 * @author Josef Kříž
 */
interface IContentExtension {


	/**
	 * @param \Nette\ComponentModel\IContainer
	 */
	public function setForm(\Nette\Forms\Container $container);


	/**
	 * @param \Nette\ComponentModel\IContainer
	 */
	public function saveForm(\Nette\Forms\Container $container, $moduleName, $moduleItemId, $linkParams);


	/**
	 * @param \Nette\ComponentModel\IContainer
	 */
	public function setValues(\Nette\Forms\Container $container, $moduleName, $moduleItemId, $linkParams);
}

