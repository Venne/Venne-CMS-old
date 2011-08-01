<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace ModulesModule;

/**
 * @author Josef Kříž
 */
class DefaultPresenter extends BasePresenter
{
	
	public function actionDefault()
	{
		$this->template->items = array();
		$items = $this->getModel()->getPackages();
		foreach($items as $key=>$item){
			$name = explode("-", $key, -1);
			$name = join("-", $name);
			$ver = str_replace($name."-", "", $key);
			$this->template->items[$name] = $this->getModel()->getPackageInfo($name, $ver);
		}
	}
	
	public function createComponentFormPackage($name)
	{
		$form = new \Venne\CMS\Modules\ModulesUploadForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Package has been uploaded");
		$form->addSubmit("submit", "Upload");
		return $form;
	}

	public function handleDelete($pkgname, $pkgver)
	{
		$this->getModel()->removePackage($pkgname, $pkgver);
		$this->flashMessage("Package has been deleted", "success");
		$this->redirect("this");
	}


	public function handleDownload($pkgname, $pkgver)
	{
		$this->getModel()->downloadPackage($pkgname, $pkgver);
	}

	public function renderDefault()
	{
		$this->setTitle("Venne:CMS | Module administration");
		$this->setKeywords("module administration");
		$this->setDescription("Module administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}
