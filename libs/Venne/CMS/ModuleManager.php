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
				if(isset($item["run"]) && $item["run"]){
					$this->modules[] = lcfirst(substr($key, 0, -6));
				}
			}
		}
		return $this->modules;
	}
	
	public function getRouteModules()
	{
		$arr = array();
		foreach($this->getModules() as $module){
			if($this->container->{$module} instanceof \Venne\CMS\Developer\IRouteModule){
				$arr[$module] = count(explode("/", $this->container->params["venne"]["modules"][$module."Module"]["routePrefix"]));
			}
		}
		asort($arr);
		$arr = array_reverse($arr);
		return array_keys($arr);
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
	
	public function getAdminModules()
	{
		$arr = array();
		foreach($this->getModules() as $module){
			if($this->container->{$module} instanceof \Venne\CMS\Developer\IAdminModule){
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
	
	public function getModuleInfo($name)
	{
		if(isset($this->container->params["venne"]["modules"][$name."Module"])){
			return $this->container->params["venne"]["modules"][$name."Module"];
		}
		return NULL;
	}


	public function getAvailableModules()
	{
		$arr = array();
		foreach(\Nette\Utils\Finder::findDirectories("*")->in(EXTENSIONS_DIR . "/modules/") as $file)
		{
			$arr[] = lcfirst(substr($file->getBaseName(), 0, -6));
		}
		foreach(\Nette\Utils\Finder::findDirectories("*")->in(VENNE_DIR . "/CMS/Modules/") as $file)
		{
			$arr[] = lcfirst(substr($file->getBaseName(), 0, -6));
		}
		return $arr;
	}
	
	public function activateModule($name)
	{
		$config = \Nette\Config\NeonAdapter::load(WWW_DIR . '/../config.neon');
		$config["common"]["venne"]["modules"][$name . "Module"]["run"] = true;
		$config["development"]["venne"]["modules"][$name . "Module"]["run"] = true;
		$config["production"]["venne"]["modules"][$name . "Module"]["run"] = true;
		$config["console"]["venne"]["modules"][$name . "Module"]["run"] = true;
		\Venne\Config\NeonAdapter::save($config, WWW_DIR . '/../config.neon', "common", array("production", "development", "console"));
	}
	
	public function deactivateModule($name)
	{
		$config = \Nette\Config\NeonAdapter::load(WWW_DIR . '/../config.neon');
		$config["common"]["venne"]["modules"][$name . "Module"]["run"] = false;
		$config["development"]["venne"]["modules"][$name . "Module"]["run"] = false;
		$config["production"]["venne"]["modules"][$name . "Module"]["run"] = false;
		$config["console"]["venne"]["modules"][$name . "Module"]["run"] = false;
		\Venne\Config\NeonAdapter::save($config, WWW_DIR . '/../config.neon', "common", array("production", "development", "console"));
	}
	
	public function installModule($name)
	{
		$config = \Nette\Config\NeonAdapter::load(WWW_DIR . '/../config.neon');
		$config["common"]["venne"]["modules"][$name . "Module"]["run"] = true;
		$config["development"]["venne"]["modules"][$name . "Module"]["run"] = true;
		$config["production"]["venne"]["modules"][$name . "Module"]["run"] = true;
		$config["console"]["venne"]["modules"][$name . "Module"]["run"] = true;
		
		/* run installation script */
		$class = "\\Venne\\CMS\\Modules\\".ucfirst($name)."Service";
		$service = new $class($this->container);
		if($service instanceof Developer\IInstallableModule){
			$service->installModule();
		}
		if($service  instanceof Developer\IRouteModule){
			$config["common"]["venne"]["modules"][$name . "Module"]["routePrefix"] = $name."/";
			$config["development"]["venne"]["modules"][$name . "Module"]["routePrefix"] = $name."/";
			$config["production"]["venne"]["modules"][$name . "Module"]["routePrefix"] = $name."/";
			$config["console"]["venne"]["modules"][$name . "Module"]["routePrefix"] = $name."/";
		}
		
		\Venne\Config\NeonAdapter::save($config, WWW_DIR . '/../config.neon', "common", array("production", "development", "console"));
	}
	
	public function uninstallModule($name)
	{
		/* run uninstallation script */
		$class = "\\Venne\\CMS\\Modules\\".ucfirst($name)."Service";
		$service = new $class($this->container);
		if($service  instanceof Developer\IInstallableModule){
			$service->uninstallModule();
		}
		
		$config = \Nette\Config\NeonAdapter::load(WWW_DIR . '/../config.neon');
		unset($config["common"]["venne"]["modules"][$name . "Module"]);
		unset($config["development"]["venne"]["modules"][$name . "Module"]);
		unset($config["production"]["venne"]["modules"][$name . "Module"]);
		unset($config["console"]["venne"]["modules"][$name . "Module"]);
		\Venne\Config\NeonAdapter::save($config, WWW_DIR . '/../config.neon', "common", array("production", "development", "console"));
	}
	
	public function saveModuleRoutePrefix($name, $prefix)
	{
		if(substr($prefix, -1, 1) != "/"){
			$prefix .= "/";
		}
		if(substr($prefix, 0, 1) == "/"){
			$prefix = substr($prefix, 1);
		} 
		
		$config = \Nette\Config\NeonAdapter::load(WWW_DIR . '/../config.neon');
		$config["common"]["venne"]["modules"][$name . "Module"]["routePrefix"] = $prefix;
		$config["development"]["venne"]["modules"][$name . "Module"]["routePrefix"] = $prefix;
		$config["production"]["venne"]["modules"][$name . "Module"]["routePrefix"] = $prefix;
		$config["console"]["venne"]["modules"][$name . "Module"]["routePrefix"] = $prefix;
		\Venne\Config\NeonAdapter::save($config, WWW_DIR . '/../config.neon', "common", array("production", "development", "console"));
	}
	
}
