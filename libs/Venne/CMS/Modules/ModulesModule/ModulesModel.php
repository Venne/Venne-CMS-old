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
		
		\Nette\Config\NeonAdapter::save($data, WWW_DIR . '/../packages/buildscripts/'.$pkgname.'.neon');
	}
	
	public function loadPackageBuild($pkgname)
	{
		return \Nette\Config\NeonAdapter::load(WWW_DIR . '/../packages/buildscripts/'.$pkgname.'.neon');
	}
	
	public function getPackageBuilds()
	{
		$data = array();
		foreach(\Nette\Utils\Finder::findFiles("*.neon")->in(WWW_DIR . '/../packages/buildscripts') as $file){
			$name = str_replace(".neon", "", $file->getFileName());
			$data[$name] = $name;
		}
		return $data;
	}
	
	public function buildPackage($pkgname)
	{
		$config = $this->loadPackageBuild($pkgname);
		$path = preg_replace('/\w+\/\.\.\//', '', WWW_DIR . '/../');
		$targetPath = preg_replace('/\w+\/\.\.\//', '', WWW_DIR . '/../packages/'.$pkgname);
		
		/*
		 * Copy files
		 */
		$zip = new \ZipArchive();
		if($zip->open(WWW_DIR . "/../packages/".$pkgname."-".$config['pkgver'].".zip", \ZipArchive::CREATE) != true){
			return false;
		}
		
		$zip->addFile(WWW_DIR . '/../packages/buildscripts/'.$pkgname.'.neon', "info.neon");
		
		foreach($config["files"] as $file){
			$dir = join("/", explode("/", $file, -1));
			$mask = str_replace($dir . "/", "", $file);

			foreach(\Nette\Utils\Finder::findFiles($mask)->from($path . "/" . $dir) as $item){
				$filePath = str_replace($path, "", $item->getRealPath());
				
				$targetDir = join("/", explode("/", $targetPath . "/" . $filePath, -1));
				//umask("0000");
				//@mkdir($targetDir, 0777, true);
				
				$zip->addFile($path . $filePath, $filePath);
				//copy($path . $filePath, $targetPath . "/" . $filePath);
			}
		}
		$zip->close();
		return true;
	}
	
	public function getPackages()
	{
		$data = array();
		foreach(\Nette\Utils\Finder::findFiles("*.zip")->in(WWW_DIR . '/../packages/') as $file){
			$name = str_replace(".zip", "", $file->getFileName());
			$data[$name] = $name;
		}
		return $data;
	}
	
	public function getPackageInfo($pkgname, $pkgver)
	{
		set_error_handler(array($this, 'handleError'));
		$zip = new \ZipArchive();
		if($zip->open(WWW_DIR . "/../packages/".$pkgname."-".$pkgver.".zip") != true){
			return false;
		}
		
		umask("0000");
		@mkdir(WWW_DIR . "/../packages/".$pkgname."-".$pkgver, 0777);
		
		$zip->extractTo(WWW_DIR . "/../packages/".$pkgname."-".$pkgver);
		$config = \Nette\Config\NeonAdapter::load(WWW_DIR . "/../packages/".$pkgname."-".$pkgver.'/info.neon');
		$zip->close();
		
		// Remove directory
		$dirContent = \Nette\Utils\Finder::find('*')->from(WWW_DIR . "/../packages/".$pkgname."-".$pkgver)->childFirst();
		foreach ($dirContent as $file) {
			if ($file->isDir())
				@rmdir($file->getPathname());
			else
				@unlink($file->getPathname());
		}
		@rmdir(WWW_DIR . "/../packages/".$pkgname."-".$pkgver);
		
		return $config;
	}
	
	/**
	 * @param string $pkgname
	 * @param string $pkgver
	 */
	public function downloadPackage($pkgname, $pkgver)
	{
		$file = file_get_contents(WWW_DIR . '/../packages/'.$pkgname."-".$pkgver.".zip");
		$httpResponse = $this->container->httpResponse;
		
		$httpResponse->setHeader('Content-Transfer-Encoding', "binary");
		$httpResponse->setHeader('Content-Description', "File Transfer");
		$httpResponse->setHeader('Content-Disposition', 'attachment; filename="' . $pkgname."-".$pkgver . '.zip"');
		$httpResponse->setContentType('application/zip', 'UTF-8');
		print($file);
		die();
	}
	
	public function uploadPackage($file)
	{
		$file->move(WWW_DIR . '/../packages/'.$file->getName());
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
		$pkgname = explode("-", str_replace(".zip", "", $file->getName()), -1);
		$pkgname = join("-", $pkgname);
		$pkgver = str_replace($pkgname . "-", "", str_replace(".zip", "", $file->getName()));
		
		
		$file->move(WWW_DIR . '/../packages/'.$file->getName());
		try{
			$this->getPackageInfo($pkgname, $pkgver);
		}catch(\Exception $ex){
			@unlink(WWW_DIR . '/../packages/'.$file->getName());
			return false;
		}
		return true;
	}


	public function removePackage($pkgname, $pkgver)
	{
		@unlink(WWW_DIR . '/../packages/'.$pkgname."-".$pkgver.".zip");
	}
	
}