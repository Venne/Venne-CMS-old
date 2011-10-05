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

	const STATUS_INSTALLED = 1;
	const STATUS_UNINSTALLED = 2;
	const STATUS_FOR_UPGRADE = 3;

	static $dir;
	static $installedDir;
	static $tempDir;
	static $infoDir;
	static $buildscriptDir;
	static $packagesDir;
	static $availableDir;

	/** @var \Venne\Application\Container */
	protected $context;


	public function __construct($context, $moduleName)
	{
		$this->context = $context;
		parent::__construct($moduleName);
		self::$dir = preg_replace('/\w+\/\.\.\//', '', $this->context->params["wwwDir"] . "/../packages");
		self::$installedDir = self::$dir . "/installed";
		self::$tempDir = self::$dir . "/temp";
		self::$infoDir = self::$dir . "/info";
		self::$buildscriptDir = self::$dir . "/buildscripts";
		self::$packagesDir = self::$dir . "/local";
		self::$availableDir = self::$dir . "/available";
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
		foreach (\Nette\Utils\Finder::findDirectories("*")->in($this->context->params["wwwDir"] . "/skins/") as $file) {
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
	
	
	/*-------------- Repositories---------------------*/
	
	/**
	 * @return array
	 */
	public function getRepositoryInfo($name)
	{
		return isset($this->context->params["venne"]["repositories"][$name]) ? $this->context->params["venne"]["repositories"][$name] : NULL;
	}
	
	public function removeRepository($name)
	{
		$config = \Nette\Config\NeonAdapter::load($this->context->params["appDir"] . '/config.neon');
		unset($config["common"]["venne"]["repositories"][$name]);
		unset($config["development"]["venne"]["repositories"][$name]);
		unset($config["production"]["venne"]["repositories"][$name]);
		unset($config["console"]["venne"]["repositories"][$name]);
		\Venne\Config\NeonAdapter::save($config, $this->context->params["appDir"] . '/config.neon', "common", array("production", "development", "console"));
	}


	public function saveRepository($name, $mirrors, $userName = NULL, $userPassword = NULL)
	{
		$config = \Nette\Config\NeonAdapter::load($this->context->params["appDir"] . '/config.neon');
		$config["common"]["venne"]["repositories"][$name]["mirrors"] = $mirrors;
		if($userName){
			$config["common"]["venne"]["repositories"][$name]["name"] = $userName;
		}else{
			if(isset($config["common"]["venne"]["repositories"][$name]["name"])){
				unset($config["common"]["venne"]["repositories"][$name]["name"]);
			}
		}
		if($userName){
			$config["common"]["venne"]["repositories"][$name]["password"] = $userPassword;
		}else{
			if(isset($config["common"]["venne"]["repositories"][$name]["password"])){
				unset($config["common"]["venne"]["repositories"][$name]["password"]);
			}
		}
		\Venne\Config\NeonAdapter::save($config, $this->context->params["appDir"] . '/config.neon', "common", array("production", "development", "console"));
	}
	
	/**
	 * @return array
	 */
	public function getRepositories()
	{
		return $this->context->params["venne"]["repositories"];
	}
	
	public function savePackageBuild($pkgname, $pkgver, $pkgdesc, $licence, $dependencies, $packager, $files)
	{
		$data = array();
		$data["pkgname"] = $pkgname;
		$data["pkgver"] = $pkgver;
		$data['pkgdesc'] = $pkgdesc;
		$data['licence'] = $licence;
		$data['dependencies'] = $dependencies;
		$data['packager'] = $packager;
		$data['files'] = $files;

		umask(0000);
		@mkdir(self::$buildscriptDir . '/' . $pkgname . '/', "0777", true);
		\Nette\Config\NeonAdapter::save($data, self::$buildscriptDir . '/' . $pkgname . '/info.neon');
	}


	public function removePackageBuild($pkgname)
	{
		unlink(self::$buildscriptDir . '/' . $pkgname . '/info.neon');
		rmdir(self::$buildscriptDir . '/' . $pkgname);
	}


	public function loadPackageBuild($pkgname)
	{
		return \Nette\Config\NeonAdapter::load(self::$buildscriptDir . '/' . $pkgname . '/info.neon');
	}


	public function getPackageBuilds()
	{
		$data = array();
		foreach (\Nette\Utils\Finder::findDirectories("*")->in(self::$buildscriptDir) as $file) {
			$name = $file->getFileName();
			$data[$name] = $name;
		}
		return $data;
	}


	public function buildPackage($pkgname)
	{
		$config = $this->loadPackageBuild($pkgname);
		$targetPath = preg_replace('/\w+\/\.\.\//', '', self::$packagesDir . '/' . $pkgname);

		/*
		 * Copy files
		 */
		$zip = new \ZipArchive();
		if ($zip->open(self::$packagesDir . "/" . $pkgname . "-" . $config['pkgver'] . ".pkg", \ZipArchive::CREATE) != true) {
			return false;
		}

		$zip->addFile(self::$buildscriptDir . '/' . $pkgname . '/info.neon', "info.neon");

		foreach ($config["files"] as $file) {
			$zip->addFile($this->context->params["wwwDir"] . "/../" . $file, $file);
		}
		$zip->close();
		return true;
	}


	public function getPackages()
	{
		$data = array();

		/* installed */
		foreach (\Nette\Utils\Finder::findDirectories("*")->in(self::$installedDir) as $file) {
			$name = $file->getFileName();
			$pkgname = explode("-", $name, -1);
			$pkgname = join("-", $pkgname);
			$pkgver = str_replace($pkgname . "-", "", $name);
			$data[$pkgname] = $this->getPackageInfo($pkgname, $pkgver);
		}

		/* in repos */
		foreach (\Nette\Utils\Finder::findDirectories("*")->in(self::$availableDir) as $file2) {
			$repo = $file2->getBaseName();
			foreach (\Nette\Utils\Finder::findDirectories("*")->in(self::$availableDir . "/" . $repo) as $file) {
				$name = $file->getBaseName();
				$config = \Nette\Config\NeonAdapter::load(self::$availableDir . "/" . $repo . "/" . $name . "/info.neon");
				$data[$name] = $config;
			}
		}

		/* in local repo */
		foreach (\Nette\Utils\Finder::findFiles("*.pkg")->in(self::$packagesDir) as $file) {
			$name = $file->getFileName();
			$pkgname = explode("-", $name, -1);
			$pkgname = join("-", $pkgname);
			$pkgver = substr(str_replace($pkgname . "-", "", $name), 0, -4);
			$data[$pkgname] = $this->getPackageInfo($pkgname, $pkgver);
		}

		ksort($data);
		return $data;
	}


	public function syncPackages()
	{
		$data = array();

		/* delete old data */
		$dirContent = \Nette\Utils\Finder::find('*')->from(self::$availableDir)->childFirst();
		foreach ($dirContent as $file) {
			if ($file->isDir())
				@rmdir($file->getPathname());
			else
				@unlink($file->getPathname());
		}

		foreach ($this->context->params["venne"]["repositories"] as $repo => $item) {
			foreach ($item["mirrors"] as $url) {
				umask(0000);
				@mkdir(self::$availableDir . "/" . $repo);

				$file = file_get_contents($url . "repository.db");
				file_put_contents(self::$tempDir . "/repository.db", $file);

				$zip = new \ZipArchive();
				if ($zip->open(self::$tempDir . "/repository.db") != true) {
					return false;
				}
				$zip->extractTo(self::$availableDir . "/" . $repo);
				$zip->close();
				
				break;
			}
		}
	}


	public function getPackageStatus($pkgname, $pkgver)
	{
		if (!file_exists(self::$infoDir . "/installed.neon")) {
			return self::STATUS_UNINSTALLED;
		}

		try {
			$config = \Nette\Config\NeonAdapter::load(self::$infoDir . "/installed.neon");
			if (!isset($config[$pkgname])) {
				return self::STATUS_UNINSTALLED;
			}
		} catch (\Exception $ex) {
			
		}

		if (version_compare($pkgver, $config[$pkgname]["pkgver"], ">")) {
			return self::STATUS_FOR_UPGRADE;
		}
		return self::STATUS_INSTALLED;
	}


	public function getPackageInfo($pkgname, $pkgver)
	{
		/* in repo */
		if (file_exists(self::$availableDir . "/" . $pkgname)) {
			$config = \Nette\Config\NeonAdapter::load(self::$availableDir . "/" . $pkgname . "/info.neon");
			if (isset($config[$pkgname]) && $config[$pkgname]["pkgver"] == $pkgver) {
				return $config[$pkgname];
			}
		}

		/* in local */
		if (file_exists(self::$packagesDir . "/" . $pkgname . "-" . $pkgver . ".pkg")) {
			return $this->getPackageInfoFromPackage($pkgname, $pkgver);
		}

		/* installed */
		if (file_exists(self::$installedDir . "/" . $pkgname . "-" . $pkgver . "/info.neon")) {
			return \Nette\Config\NeonAdapter::load(self::$installedDir . "/" . $pkgname . "-" . $pkgver . "/info.neon");
		}
		return false;
	}


	public function getPackageInfoFromPackage($pkgname, $pkgver)
	{
		set_error_handler(array($this, 'handleError'));
		$zip = new \ZipArchive();
		if ($zip->open(self::$packagesDir . "/" . $pkgname . "-" . $pkgver . ".pkg") != true) {
			return false;
		}

		umask(0000);
		@mkdir(self::$tempDir . "/" . $pkgname . "-" . $pkgver, 0777);

		$zip->extractTo(self::$tempDir . "/" . $pkgname . "-" . $pkgver . "/");
		$config = \Nette\Config\NeonAdapter::load(self::$tempDir . "/" . $pkgname . "-" . $pkgver . '/info.neon');
		$zip->close();


		// Remove directory
		$dirContent = \Nette\Utils\Finder::find('*')->from(self::$tempDir . "/" . $pkgname . "-" . $pkgver)->childFirst();
		foreach ($dirContent as $file) {
			if ($file->isDir())
				@rmdir($file->getPathname());
			else
				@unlink($file->getPathname());
		}
		@rmdir(self::$tempDir . "/" . $pkgname . "-" . $pkgver);

		return $config;
	}


	/**
	 * @param string $pkgname
	 * @param string $pkgver
	 */
	public function sendPackage($pkgname, $pkgver)
	{
		$file = file_get_contents(self::$packagesDir . '/' . $pkgname . "-" . $pkgver . ".pkg");
		$httpResponse = $this->context->httpResponse;

		$httpResponse->setHeader('Content-Transfer-Encoding', "binary");
		$httpResponse->setHeader('Content-Description', "File Transfer");
		$httpResponse->setHeader('Content-Disposition', 'attachment; filename="' . $pkgname . "-" . $pkgver . '.pkg"');
		$httpResponse->setContentType('application/zip', 'UTF-8');
		print($file);
		die();
	}


	public function uploadPackage($file)
	{
		$file->move(self::$packagesDir . '/' . $file->getName());
	}


	public function handleError()
	{
		
	}


	/**
	 * @param \Nette\Forms\IControl $control
	 * @return bool 
	 */
	public function isUploadPackageValid(\Nette\Forms\IControl $control)
	{
		$file = $control->getValue();
		$pkgname = explode("-", str_replace(".pkg", "", $file->getName()), -1);
		$pkgname = join("-", $pkgname);
		$pkgver = str_replace($pkgname . "-", "", str_replace(".pkg", "", $file->getName()));


		$file->move(self::$packagesDir . '/' . $file->getName());
		try {
			$this->getPackageInfo($pkgname, $pkgver);
		} catch (\Exception $ex) {
			@unlink(self::$packagesDir . '/' . $file->getName());
			return false;
		}
		return true;
	}


	public function removePackage($pkgname, $pkgver)
	{
		@unlink(self::$packagesDir . '/' . $pkgname . "-" . $pkgver . ".pkg");
	}


	public function downloadPackage($pkgname, $pkgver)
	{
		set_error_handler(array($this, 'handleError'));
		foreach ($this->context->params["venne"]["repositories"] as $repo => $item) {

			if (file_exists(self::$availableDir . "/" . $repo . "/" . $pkgname)) {
				
				$config = \Nette\Config\NeonAdapter::load(self::$availableDir . "/" . $repo . "/" . $pkgname . "/info.neon");
				if (!isset($config["pkgver"]) || $config["pkgver"] != $pkgver) {
					continue;
				}
				
				foreach ($item["mirrors"] as $url) {
					$fileName = $pkgname . "-" . $pkgver . ".pkg";
					dump($url . $fileName);
					$file = file_get_contents($url . $fileName);
					if(!$file)						continue;
					file_put_contents(self::$packagesDir . "/" . $fileName, $file);
					
					return true;
				}
			}
		}
		die();
		return false;
	}


	public function installPackage($pkgname, $pkgver)
	{
		if (!file_exists(self::$packagesDir . "/" . $pkgname . "-" . $pkgver . ".pkg")) {
			if(!$this->downloadPackage($pkgname, $pkgver)){
				return false;
			}
		}

		/* Extract */
		$zip = new \ZipArchive();
		if ($zip->open(self::$packagesDir . "/" . $pkgname . "-" . $pkgver . ".pkg") != true) {
			return false;
		}
		umask(0000);
		@mkdir(self::$installedDir . "/" . $pkgname . "-" . $pkgver, 0777, true);
		$zip->extractTo(self::$installedDir . "/" . $pkgname . "-" . $pkgver);
		$zip->close();

		/* copy files */
		$dirContent = \Nette\Utils\Finder::find('*')->from(self::$installedDir . "/" . $pkgname . "-" . $pkgver)->childFirst();
		foreach ($dirContent as $file) {
			if ($file->getBaseName() == "info.neon")
				continue;
			$name = str_replace(self::$installedDir . "/" . $pkgname . "-" . $pkgver . "/", "", $file->getPathName());
			if ($file->isDir()) {
				umask(0000);
				@mkdir($this->context->params["wwwDir"] . "/../" . $name, 0777, true);
				rmdir($file->getPathname());
			} else {
				umask(0000);
				@mkdir(dirname($this->context->params["wwwDir"] . "/../" . $name), 0777, true);
				copy($file->getPathName(), $this->context->params["wwwDir"] . "/../" . $name);
				unlink($file->getPathname());
			}
		}

		/* config */
		if (file_exists($this->context->params["wwwDir"] . "/../packages/info/installed.neon")) {
			$config = \Nette\Config\NeonAdapter::load(self::$infoDir . "/installed.neon");
		} else {
			$config = array();
		}
		$info = $this->getPackageInfo($pkgname, $pkgver);
		$config[$pkgname] = array();
		$config[$pkgname]["pkgver"] = $info["pkgver"];
		$config[$pkgname]["pkgdesc"] = $info["pkgdesc"];
		$config[$pkgname]["licence"] = $info["licence"];
		\Nette\Config\NeonAdapter::save($config, self::$infoDir . "/installed.neon");
		return true;
	}


	public function uninstallPackage($pkgname, $pkgver)
	{
		$config = \Nette\Config\NeonAdapter::load(self::$installedDir . "/" . $pkgname . "-" . $pkgver . "/info.neon");
		foreach ($config["files"] as $file) {
			$dir = dirname($this->context->params["wwwDir"] . "/../" . $file);
			unlink($this->context->params["wwwDir"] . "/../" . $file);
			
			while(!($files = @scandir($dir)) || !(count($files) > 2)) {
				dump($dir);
				rmdir($dir);
				$dir = dirname($dir);
			}
		}

		unlink(self::$installedDir . "/" . $pkgname . "-" . $pkgver . "/info.neon");
		rmdir(self::$installedDir . "/" . $pkgname . "-" . $pkgver);

		/* config */
		$config = \Nette\Config\NeonAdapter::load(self::$infoDir . "/installed.neon");
		unset($config[$pkgname]);
		\Nette\Config\NeonAdapter::save($config, self::$infoDir . "/installed.neon");
	}


	public function upgradePackage($pkgname, $pkgver)
	{
		$this->installPackage($pkgname, $pkgver);
	}

}

