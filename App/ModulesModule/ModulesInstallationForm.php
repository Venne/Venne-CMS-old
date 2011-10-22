<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\ModulesModule;

use Venne\ORM\Column;
use Nette\Utils\Html;
use Venne\Forms\Form;

/**
 * @author Josef Kříž
 */
class ModulesInstalltionForm extends \Venne\Developer\Form\EditForm {

	public function startup()
	{
		parent::startup();
		$model = $this->presenter->context->services->packages;
		
		foreach($model->getPackages() as $key=>$package){
			$ver = str_replace("-", "pp", str_replace(".", "p", $package["pkgver"]));
			$this->addSelect("item_" . $key . "_" . $ver, NULL);
			$status = $model->getPackageStatus($key, $package["pkgver"]);
			if($status == $model::STATUS_INSTALLED){
				$arr = array("uninstall"=>"unninstall");
				$this["item_" . $key . "_" . $ver]->setItems($arr)->setPrompt("-- installed --");
			}else if($status == $model::STATUS_FOR_UPGRADE){
				$arr = array("uninstall"=>"unninstall", "upgrade"=>"upgrade");
				$this["item_" . $key . "_" . $ver]->setItems($arr)->setPrompt("-- new version --");
			}else{
				$arr = array("install"=>"install");
				$this["item_" . $key . "_" . $ver]->setItems($arr)->setPrompt("");
			}
		}
	}


	public function load()
	{

	}

	public function save()
	{
		$model = $this->getPresenter()->getContext()->cms->modules->model;
		
		foreach($model->getPackages() as $key=>$package){
			$ver = str_replace("-", "pp", str_replace(".", "p", $package["pkgver"]));
			$value = $this["item_" . $key . "_" . $ver]->getValue();
			
			if($value == "install"){
				if(!$model->installPackage($key, $package["pkgver"])){
					$this->setFlashMessage("package can not be downloaded", "warning");
				}
			}else if($value == "uninstall"){
				$model->uninstallPackage($key, $package["pkgver"]);
			}else if($value == "upgrade"){
				$model->upgradePackage($key, $package["pkgver"]);
			}
		}
	}

}
