<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Model;

/**
 * @author Josef Kříž
 * 
 * @property-read \Nette\DI\Container $container
 */
class BaseModel extends \Nette\Object {


	/** @var \Venne\Developer\Service\BaseService */
	protected $service;


	/**
	 * @param \Venne\Developer\Service\BaseService
	 */
	public function __construct($service)
	{
		$this->service = $service;
	}
	
	public function getContainer()
	{
		return $this->service->getContainer();
	}

}

