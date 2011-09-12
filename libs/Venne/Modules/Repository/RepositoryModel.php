<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Modules;

use Venne;

/**
 * @author Josef Kříž
 */
class RepositoryModel extends Venne\Developer\Model\BaseModel {


	protected $dir;


	public function __construct(\Nette\DI\Container $container, $parent)
	{
		parent::__construct($container, $parent);
		$this->dir = DATA_DIR . "/modules/repository";
	}


	public function getRepositories()
	{
		$arr = array();
		foreach (\Nette\Utils\Finder::findDirectories("*")->in($this->dir) as $file) {
			$arr[$file->getBaseName()] = $file->getBaseName();
		}
		return $arr;
	}


	public function saveRepository($name)
	{
		umask(0000);
		@mkdir($this->dir . "/" . $name);
	}


	public function renameRepository($name, $oldName)
	{
		rename($this->dir . "/" . $oldName, $this->dir . "/" . $name);
	}


	public function removeRepository($name)
	{
		$dirContent = \Nette\Utils\Finder::find('*')->from($this->dir . "/" . $name)->childFirst();
		foreach ($dirContent as $file) {
			if ($file->isDir())
				@rmdir($file->getPathname());
			else
				@unlink($file->getPathname());
		}
		@rmdir($this->dir . "/" . $name);
	}


	public function getPackages($repositoryName)
	{
		$arr = array();
		foreach (\Nette\Utils\Finder::findFiles("*.pkg")->in($this->dir . "/" . $repositoryName) as $file) {
			if ($file->getBaseName() == "repository.pkg") {
				continue;
			}
			$arr[] = substr($file->getBaseName(), 0, -4);
		}
		sort($arr);
		return $arr;
	}


	public function buildRepository($name)
	{
		umask(0000);
		@mkdir(TEMP_DIR . "/repository", 0777);
		@unlink($this->dir . "/" . $name . "/repository.db");

		dump("ok");

		$zip2 = new \ZipArchive();
		if ($zip2->open($this->dir . "/" . $name . "/repository.db", \ZipArchive::CREATE) != true) {
			return false;
		}
		
		foreach (\Nette\Utils\Finder::findFiles("*.pkg")->in($this->dir . "/" . $name) as $file) {

			dump($file->getPathname());

			if ($file->getBaseName() == "repository.pkg") {
				continue;
			}
			$name = $file->getFileName();
			$pkgname = explode("-", $name, -1);
			$pkgname = join("-", $pkgname);

			@mkdir(TEMP_DIR . "/repository/" . $pkgname . "/", 0777);

			$zip = new \ZipArchive();
			if ($zip->open($file->getPathname()) != true) {
				return false;
			}
			$zip->extractTo(TEMP_DIR . "/repository/" . $pkgname . "/");
			$zip->close();
			
			$zip2->addFile(TEMP_DIR . "/repository/" . $pkgname . '/info.neon', $pkgname . "/info.neon");

		}
		$zip2->close();

		$dirContent = \Nette\Utils\Finder::find('*')->from(TEMP_DIR . "/repository/")->childFirst();
		foreach ($dirContent as $file) {
			if ($file->isDir())
				@rmdir($file->getPathname());
			else
				@unlink($file->getPathname());
		}
		rmdir(TEMP_DIR . "/repository/");

	}


	public function removePackage($name, $repositoryName)
	{
		unlink($this->dir . "/" . $repositoryName . "/" . $name . ".pkg");
		$this->buildRepository($repositoryName);
	}


	public function uploadPackage($file, $repositoryName)
	{
		$file->move($this->dir . "/" . $repositoryName . "/" . $file->getName());
		$this->buildRepository($repositoryName);
	}


	/**
	 * @param string $pkgname
	 * @param string $pkgver
	 */
	public function sendPackage($file, $repositoryName)
	{
		return new \Nette\Application\Responses\FileResponse($this->dir . "/" . $repositoryName . '/' . $file);
	}

}

