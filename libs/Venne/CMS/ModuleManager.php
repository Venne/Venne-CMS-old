<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS;

use Venne,
	Nette\Application\Routers\SimpleRouter,
	Nette\Application\Routers\Route;

/**
 * @author Josef Kříž
 */
class ModuleManager {
	
	protected $modules;
	
	/** @var Nette\DI\Container */
	protected $container;
	
	public function __construct($container)
	{
		$this->container = $container;
	}
	
	public function getModules()
	{
		if(!$this->modules){
			$modules = $this->container->params["venne"]["modules"];
			foreach($modules as $key=>$item){
				$this->modules[] = lcfirst(substr($key, 0, -6));
			}
		}
		return $this->modules;
	}
	
	public function getRouteModules()
	{
		$arr = array();
		foreach($this->getModules() as $module){
			if($this->container->{$module} instanceof \Venne\CMS\Developer\IRouteModule){
				$arr[] = $module;
			}
		}
		return $arr;
	}
	
	public function getSitemapModules()
	{
		$arr = array();
		foreach($this->getModules() as $module){
			if($this->container->{$module} instanceof \Venne\CMS\Developer\ISitemapModule){
				$arr[] = $module;
			}
		}
		return $arr;
	}
	
	public function getContentExtensionModules()
	{
		$arr = array();
		foreach($this->getModules() as $module){
			if($this->container->{$module} instanceof \Venne\CMS\Developer\IContentExtensionModule){
				$arr[] = $module;
			}
		}
		return $arr;
	}
	
	public function getRenderableContentExtensionModules()
	{
		$arr = array();
		foreach($this->getModules() as $module){
			if($this->container->{$module} instanceof \Venne\CMS\Developer\IRenderableContentExtensionModule){
				$arr[] = $module;
			}
		}
		return $arr;
	}
	
	public function getCallbackModules()
	{
		$arr = array();
		foreach($this->getModules() as $module){
			if($this->container->{$module} instanceof \Venne\CMS\Developer\ICallbackModule){
				$arr[] = $module;
			}
		}
		return $arr;
	}
	
}
