<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\ModulesModule;

use Venne;

/**
 * @author Josef Kříž
 */
class Service extends \Venne\Developer\Service\BaseService {


	/** @var \Venne\Application\Container */
	protected $context;


	public function __construct($context, $moduleName)
	{
		$this->context = $context;
		parent::__construct($moduleName);
	}


	public function getAvailableModules()
	{
		$ret = array();
		foreach (\Nette\Utils\Finder::findDirectories("*")->in($this->context->params["appDir"]) as $file) {
			$module = lcfirst(substr($file->getBaseName(), 0, -6));
			if ($module != "default" && $module != "admin" && $module != "installation" && $this->moduleExist($module)) {
				$ret[] = $module;
			}
		}
		return $ret;
	}


	public function getFrontModules()
	{
		$arr = array();
		foreach ($this->context->modules->getServiceNames() as $module) {
			if (file_exists($this->context->params["appDir"] . "/" . ucfirst($module) . "Module")) {
				$arr[] = $module;
			}
		}
		return $arr;
	}


	public function getModuleInfo($moduleName)
	{
		return array(
			"name" => $moduleName,
			"description" => $this->getModuleDescription($moduleName),
			"version" => $this->getModuleVersion($moduleName),
		);
	}


	public function getModuleDescription($moduleName)
	{
		if ($this->context->modules->hasService($moduleName)) {
			return $this->context->modules->$moduleName->getDescription();
		}
		$class = $this->getInstanceOfModule($moduleName);
		return $class->getDescription();
	}


	public function getModuleVersion($moduleName)
	{
		if ($this->context->modules->hasService($moduleName)) {
			return $this->context->modules->$moduleName->getVersion();
		}
		$class = $this->getInstanceOfModule($moduleName);
		return $class->getVersion();
	}


	protected function moduleExist($moduleName)
	{
		$class = "{$moduleName}Module\\Module";
		foreach ($this->context->params["venne"]["moduleNamespaces"] as $ns) {
			if (class_exists($ns . $class)) {
				$class = $ns . $class;
				break;
			}
		}
		if (class_exists($class)) {
			return true;
		}
		return false;
	}


	protected function getInstanceOfModule($moduleName)
	{
		$class = "{$moduleName}Module\\Module";
		foreach ($this->context->params["venne"]["moduleNamespaces"] as $ns) {
			if (class_exists($ns . $class)) {
				$class = $ns . $class;
				break;
			}
		}
		if (class_exists($class)) {
			return new $class;
		}
		throw new \Exception("Module `$moduleName` not exist");
	}


	public function getPresenters($module)
	{
		$data = array();
		$dir = $this->context->params["appDir"] . "/" . ucfirst($module) . "Module";
		if (file_exists($dir)) {
			foreach (\Nette\Utils\Finder::findFiles("*Presenter.php")->from($dir) as $file) {
				$data[] = substr($file->getBaseName(), 0, -13);
			}
		}
		return $data;
	}


	public function getActions($module, $presenter)
	{
		$data = array();
		$dir = $this->context->params["appDir"] . "/" . ucfirst($module) . "Module/templates/" . ucfirst($presenter);
		if (file_exists($dir)) {
			foreach (\Nette\Utils\Finder::findFiles("*")->from($dir) as $file) {
				$data[] = substr($file->getBaseName(), 0, -6);
			}
		}
		return $data;
	}


	public function getParams($module, $presenter)
	{
		$data = array();
		$file = $this->context->params["appDir"] . '/' . ucfirst($module) . "Module/presenters/" . ucfirst($presenter) . "Presenter.php";

		if (file_exists($file)) {
			$text = file_get_contents($file);
			preg_match_all('/@persistent(.*?)\\n(.*?)public(.*?)\$(.*?)[;= ]/', $text, $matches);

			foreach ($matches[4] as $item) {
				$data[] = $item;
			}
		}
		return $data;
	}


	public function getSkins()
	{
		$data = array();
		foreach (\Nette\Utils\Finder::findDirectories("*")->in($this->context->params["extensionsDir"] . "/skins/") as $file) {
			$data[$file->getBaseName()] = $file->getBaseName();
		}
		return $data;
	}


	public function getLayouts()
	{
		$data = array();
		foreach (\Nette\Utils\Finder::findFiles("@*.latte")->in($this->context->params["wwwDir"] . "/skins/" . $this->context->params["venne"]["website"]["template"] . "/layouts/") as $file) {
			$data[substr($file->getBaseName(), 1, -6)] = substr($file->getBaseName(), 1, -6);
		}
		return $data;
	}

}

