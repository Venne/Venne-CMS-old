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
class ModulesModel extends Venne\CMS\Developer\Model {


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


	public function __construct(\Nette\DI\Container $container, $parent)
	{
		parent::__construct($container, $parent);
		self::$dir = preg_replace('/\w+\/\.\.\//', '', WWW_DIR . "/../packages");
		self::$installedDir = self::$dir . "/installed";
		self::$tempDir = self::$dir . "/temp";
		self::$infoDir = self::$dir . "/info";
		self::$buildscriptDir = self::$dir . "/buildscripts";
		self::$packagesDir = self::$dir . "/local";
		self::$availableDir = self::$dir . "/available";
	}

	/**
	 * @return array
	 */
	public function getRepositories()
	{
		return $this->container->params["venne"]["repositories"];
	}
	
	/**
	 * @return array
	 */
	public function getRepositoryInfo($name)
	{
		$config = \Nette\Config\NeonAdapter::load(WWW_DIR . '/../config.neon');
		return $config["common"]["venne"]["repositories"][$name];
	}
	
	public function removeRepository($name)
	{
		$config = \Nette\Config\NeonAdapter::load(WWW_DIR . '/../config.neon');
		unset($config["common"]["venne"]["repositories"][$name]);
		unset($config["development"]["venne"]["repositories"][$name]);
		unset($config["production"]["venne"]["repositories"][$name]);
		unset($config["console"]["venne"]["repositories"][$name]);
		\Venne\Config\NeonAdapter::save($config, WWW_DIR . '/../config.neon', "common", array("production", "development", "console"));
	}


	public function saveRepository($name, $mirrors, $userName = NULL, $userPassword = NULL)
	{
		$config = \Nette\Config\NeonAdapter::load(WWW_DIR . '/../config.neon');
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
		\Venne\Config\NeonAdapter::save($config, WWW_DIR . '/../config.neon', "common", array("production", "development", "console"));
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
			$zip->addFile(WWW_DIR . "/../" . $file, $file);
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

		foreach ($this->container->params["venne"]["repositories"] as $repo => $item) {
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
		$httpResponse = $this->container->httpResponse;

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
		foreach ($this->container->params["venne"]["repositories"] as $repo => $item) {

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
		@mkdir(self::$installedDir . "/" . $pkgname . "-" . $pkgver, 0777);
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
				@mkdir(WWW_DIR . "/../" . $name, 0777);
				rmdir($file->getPathname());
			} else {
				copy($file->getPathName(), WWW_DIR . "/../" . $name);
				unlink($file->getPathname());
			}
		}

		/* config */
		if (file_exists(WWW_DIR . "/../packages/info/installed.neon")) {
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
			$dir = dirname(WWW_DIR . "/../" . $file);
			unlink(WWW_DIR . "/../" . $file);
			
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
	
//	public function uploadPackage($pkgname, $pkgver, $repository, $user = NULL, $pass = NULL)
//	{
//		$file = WWW_DIR . "/../packages/".$pkgname."-".$pkgver.".zip";
// 
//		$c = curl_init();
//		curl_setopt($c, CURLOPT_URL, $repository . "index.php");
//		curl_setopt($c, CURLOPT_USERPWD, "username:password");
//		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
//		curl_setopt($c, CURLOPT_PUT, true);
//		curl_setopt($c, CURLOPT_INFILESIZE, filesize($file));
//
//		$fp = fopen($file, "r");
//		curl_setopt($c, CURLOPT_INFILE, $fp);
//
//		curl_exec($c);
//
//		curl_close($c);
//		fclose($fp); 
//	}
}