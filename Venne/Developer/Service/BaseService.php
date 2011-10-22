<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Service;

use Venne;

/**
 * @author Josef Kříž
 * @author	Patrik Votoček
 */
class BaseService extends \Nette\Object {

	/** @var string */
	protected $moduleName;
	
	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct($moduleName)
	{
		$this->moduleName = $moduleName;
	}
	
	/**
	 * @return string 
	 */
	public function getModuleName()
	{
		return $this->moduleName;
	}

}
