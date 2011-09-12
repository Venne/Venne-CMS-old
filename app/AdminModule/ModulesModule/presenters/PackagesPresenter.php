<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace AdminModule\ModulesModule;

/**
 * @author Josef Kříž
 */
class PackagesPresenter extends BasePresenter
{
	
	public function startup()
	{
		parent::startup();
		$this->addPath("Packages", $this->link(":Modules:Packages:"));
	}
	
	public function actionDefault()
	{
		$this->template->items = $this->context->services->packages->getPackages();
	}
	
	public function actionAvailable()
	{
		$this->template->items = array();
		$items = $this->getModel()->getPackages();
		foreach($items as $key=>$item){
			$name = explode("-", $key, -1);
			$name = join("-", $name);
			$ver = str_replace($name."-", "", $key);
			$this->template->items[$name] = $this->context->services->packages->getPackageInfo($name, $ver);
		}
	}
	
	public function createComponentForm($name)
	{
		$form = new \Venne\Modules\ModulesInstalltionForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Changes has been saved");
		$form->setSubmitLabel("Apply");
		return $form;
	}


	public function createComponentFormPackage($name)
	{
		$form = new \Venne\Modules\ModulesUploadForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Package has been uploaded");
		$form->setSubmitLabel("Upload");
		return $form;
	}
	
	public function handleSync()
	{
		$this->getModel()->syncPackages();
		$this->flashMessage("Packages has been synced");
		$this->redirect("this");
	}

	public function handleDelete($pkgname, $pkgver)
	{
		$this->getModel()->removePackage($pkgname, $pkgver);
		$this->flashMessage("Package has been deleted", "success");
		$this->redirect("this");
	}


	public function handleDownload($pkgname, $pkgver)
	{
		$this->getModel()->sendPackage($pkgname, $pkgver);
	}

	public function renderDefault()
	{
		$this->setTitle("Venne:CMS | Module administration");
		$this->setKeywords("module administration");
		$this->setDescription("Module administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}
