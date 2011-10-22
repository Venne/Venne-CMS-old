<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\DI;

use Venne;

/**
 * @author Josef Kříž
 * 
 * @property-read \Venne\Application\Container $context
 */
class Container extends \Nette\DI\Container {


	/** @var array */
	protected $serviceNames;


	/**
	 * Adds the specified service or service factory to the container.
	 * @param  string
	 * @param  mixed   object, class name or callback
	 * @param  mixed   array of tags or string typeHint
	 * @return Container|ServiceBuilder  provides a fluent interface
	 */
	public function addService($name, $service, $tags = NULL)
	{
		$this->serviceNames[$name] = $name;
		return parent::addService($name, $service, $tags);
	}


	public function __construct(\Venne\Application\Container $context)
	{
		$this->addService("context", $context);
	}


	public function getServiceNames()
	{
		//die(dump($this->serviceNames));
		return $this->serviceNames;
	}


	public function getServices()
	{
		$ret = array();
		foreach ($this->serviceNames as $service) {
			$ret[] = $this->getService($service);
		}
		return $ret;
	}


	public function getServicesByInterface($interfaceName)
	{
		$ret = array();
		foreach ($this->getServices() as $service) {
			if (is_a($service, $interfaceName)) {
				$ret[] = $service;
			}
		}
		return $ret;
	}

}