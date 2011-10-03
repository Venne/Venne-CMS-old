<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace InstallationModule\AdminModule;

use \Nette\Application\UI\Form;

/**
 * @author Josef Kříž
 */
class DefaultPresenter extends \Venne\Developer\Presenter\InstallationPresenter
{
	
	public function startup()
	{
		
		parent::startup();

		/*
		 * Extensions
		 */
		$modules = array("gd", "gettext", "iconv", "json", "pdo", "pdo_mysql");
		foreach ($modules as $item) {
			if (!extension_loaded($item)) {
				$this->flashMessage("Module " . $item . " is not enabled.", "warning");
			}
		}
		
		/*
		 * Writable
		 */
		$paths = array($this->getContext()->params["extensionsDir"], $this->getContext()->params["wwwDir"] . "/public/", $this->getContext()->params["wwwDir"] . "/skins/", $this->getContext()->params["appDir"] . "/config.neon", $this->getContext()->params["flagsDir"]);
		foreach ($paths as $item) {
			if (!is_writable($item)) {
				$this->flashMessage("Path " . $item . " is not writable.", "warning");
			}
		}
		
		if(file_exists($this->getContext()->params["flagsDir"] . "/installed")){
			$this->setView("finish");
		}else{
			$this->setView("default");
			if($this->context->params["venne"]["admin"]["password"]){
				$this->setView("database");
				if($this->context->params["database"]["dbname"]){
					$this->setView("website");
				}
			}
		}
	}
	
	public function actionFinish()
	{
		$this->context->services->system->setInstallationDone();
		$this->redirect("default");
	}
	
	/*public function actionDatabase()
	{
		$em = $this->context->doctrineContainer->entityManager;
	
		$classes = array();
		$robotLoader = $this->context->robotLoader;
		$robotLoader->rebuild();
		foreach($robotLoader->getIndexedClasses() as $key=>$item){
			$class = "\\{$key}";
			$classReflection = new \Nette\Reflection\ClassType($class);
			if($classReflection->isSubclassOf("\\Venne\\Developer\\Doctrine\\BaseEntity")){
				dump($class);
				$classes[] = $em->getClassMetadata($class);
			}
		}
		$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
		dump($tool->getCreateSchemaSql($classes));
		$tool->createSchema($classes);
		//$sm = $this->context->doctrineContainer->schemaManager;
		//$fromSchema = $sm->createSchema();
		//dump($fromSchema);
		die("ok");
	}*/

	
	public function createComponentFormAccount($name)
	{
		$form = new \Venne\Modules\SystemAccountForm($this, $name, "common");
		$form->setSuccessLink("this");
		$form->setSubmitLabel("Next");
		return $form;
	}
	
	public function createComponentFormDatabase($name)
	{
		$form = new \Venne\Modules\SystemDatabaseForm($this, $name, "common", false, true);
		$form->setSuccessLink("this");
		//$form->setFlashMessage("Database settings has been updated");
		$form->setSubmitLabel("Install");
		return $form;
	}
	
	public function createComponentFormWebsite($name)
	{
		$form = new \Venne\Modules\WebsiteForm($this, $name);
		$form->setSuccessLink("finish");
		$form->setSubmitLabel("Install");
		return $form;
	}
	
	public function renderDefault()
	{

	}
	
	public function beforeRender()
	{
		parent::beforeRender();
		$this->template->websiteUrl = $this->getHttpRequest()->getUrl()->getBaseUrl();
		$this->template->installationMode = true;
		$this->template->hideMenuItems = true;
		
		$this->setTitle("Venne:CMS | Installation");
		$this->setKeywords("installation");
		$this->setDescription("Installation");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}
