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
class Model{
	
	/** @var \Nette\DI\Container */
	protected $container;
	
	protected $parent;
	
	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(\Nette\DI\Container $container, $parent)
	{
		$this->container = $container;
		$this->parent = $parent;
	}
	
	public function getRepository()
	{
		return $this->parent->getRepository();
	}
	
	public function getEntityManager()
	{
		return $this->parent->getEntityManager();
	}
	
	/**
	 * @return \Nette\DI\Container
	 */
	public function getContainer()
	{
		return $this->container;
	}

}

