<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Modules;

use Venne;

/**
 * @author Josef Kříž
 */
class RepositoryModel extends Venne\CMS\Developer\Model {

	protected $dir;
	
	public function __construct(\Nette\DI\Container $container, $parent)
	{
		parent::__construct($container, $parent);
		$this->dir = DATA_DIR . "/modules/repository";
	}
	
	public function getRepositories()
	{
		$arr = array();
		foreach(\Nette\Utils\Finder::findDirectories("*")->in($this->dir) as $file){
			$arr[$file->getBaseName()] = $file->getBaseName();
		}
		return $arr;
	}
	
	public function saveRepository($name)
	{
		umask(0000);
		@mkdir($this->dir . "/".$name);
	}
	
	public function renameRepository($name, $oldName)
	{
		rename($this->dir . "/".$oldName, $this->dir . "/".$name);
	}


	public function removeRepository($name)
	{
		$dirContent = \Nette\Utils\Finder::find('*')->from($this->dir . "/".$name)->childFirst();
		foreach ($dirContent as $file) {
			if ($file->isDir())
				@rmdir($file->getPathname());
			else
				@unlink($file->getPathname());
		}
		@rmdir($this->dir . "/".$name);
	}
	
	public function getPackages($repositoryName)
	{
		$arr = array();
		foreach(\Nette\Utils\Finder::findFiles("*.pkg")->in($this->dir."/".$repositoryName) as $file){
			if($file->getBaseName() == "repository.pkg"){
				continue;
			}
			$arr[] = substr($file->getBaseName(), 0, -4);
		}
		sort($arr);
		return $arr;
	}
	
	public function buildRepository($name)
	{
		
	}


	public function removePackage($name, $repositoryName)
	{
		unlink($this->dir."/".$repositoryName."/".$name.".pkg");
		$this->buildRepository($repositoryName);
	}
	
	public function uploadPackage($file, $repositoryName)
	{
		$file->move($this->dir."/".$repositoryName."/" . $file->getName());
	}
	
	/**
	 * @param string $pkgname
	 * @param string $pkgver
	 */
	public function sendPackage($file, $repositoryName)
	{
		return new \Nette\Application\Responses\FileResponse($this->dir."/".$repositoryName . '/' . $file);
	}
	
	
}

