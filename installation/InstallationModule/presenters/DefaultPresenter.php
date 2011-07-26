<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace InstallationModule;

use \Nette\Application\UI\Form;

/**
 * @author Josef Kříž
 */
class DefaultPresenter extends \Venne\CMS\Developer\Presenter\InstallationPresenter
{
	
	public function startup()
	{
		
		parent::startup();
		if(file_exists(WWW_DIR . "/../temp/installed") && $this->getAction() != "finish" && $this->getAction() != "installed"){
			$this->redirect("installed");
		}
		
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
		$paths = array(WWW_DIR . "/../app/", WWW_DIR . "/public/", WWW_DIR . "/templates/");
		foreach ($paths as $item) {
			if (!is_writable($item)) {
				$this->flashMessage("Path " . $item . " is not writable.", "warning");
			}
		}
		
	}
	
	public function actionFinish()
	{
		$this->getContext()->configurator->setInstallationDone();
		$this->template->websiteUrl = $this->getHttpRequest()->getUrl()->getBaseUrl();
	}


	public function createComponentFormAccount($name)
	{
		$form = new Form($this, $name);

		//$form->setTranslator($this->getContext()->translator);
		
		$form->addGroup();
		$form->addText("name", "Name")->addRule(Form::FILLED, 'Enter name');
		$form->addPassword("password", "Password")
				->setOption("description", "minimal length is 5 char")
				->addRule(Form::FILLED, 'Enter password')
				->addRule(Form::MIN_LENGTH, 'Password is short', 5);
		$form->addPassword("password_confirm", "Confirm password")
				->addRule(Form::EQUAL, 'Invalid re password', $form['password']);

		$form->setCurrentGroup();
		$form->addSubmit("save", "Next");
		$form->onSuccess[] = array($this, 'handleAccountSave');
		$form->addProtection("Access reject");
		return $form;
	}
	
	public function createComponentFormDatabase($name)
	{
		$form = new Form($this, $name);

		$form->addGroup();
		$form->addSelect("driver", "Driver", array("pdo_mysql"=>"pdo_mysql", "pdo_pgsql"=>"pdo_pgsql"));
		$form->addText("host", "Host")->addRule(Form::FILLED, 'Enter host');
		$form->addText("user", "User name")->addRule(Form::FILLED, 'Enter user name');
		$form->addPassword("password", "Password");
		$form->addText("dbname", "Database")->addRule(Form::FILLED, 'Enter database name');

		$form->setCurrentGroup();
		$form->addSubmit("submit"," Next");
		$form->onSuccess[] = array($this, "handleDatabaseSave");
		return $form;
	}
	
	public function createComponentFormWebsite($name)
	{
		$form = new \Venne\CMS\Modules\WebsiteForm($this, $name);
		$form->setSuccessLink("finish");
		$form->addSubmit("submit"," Install");
		return $form;
	}
	
	public function handleAccountSave($form)
	{
		if(file_exists(WWW_DIR . "/../temp/installed")){
			$this->flashMessage("Application Venne:CMS is already installed", "error");
			$this->redirect("this");
		}else{
			$this->getContext()->configurator->setAdminAccount($form["name"]->getValue(), $form["password"]->getValue());
			$this->redirect("database");
		}
	}
	
	protected function handleError()
	{
		//$this->flashMessage("Cannot connect to database", "error");
		//$this->redirect("this");
	}
	
	public function handleDatabaseSave($form)
	{
		$config = $form->getValues();
		set_error_handler(array($this, 'handleError'));
		try{
			$db = new \PDO(substr($config["driver"], 4) . ':host=' . $config["host"] . ';dbname=' . $config["dbname"], $config["user"], $config["password"]);
			$this->getContext()->configurator->setDatabase($form["driver"]->getValue(), $form["host"]->getValue(), $form["dbname"]->getValue(), $form["user"]->getValue(), $form["password"]->getValue());
			$this->getContext()->configurator->createDatabaseStructure();
			$this->redirect("website");
		}catch(\PDOException $e){
			$this->flashMessage("Cannot connect to database ".$e->getMessage(), "error");
		}
		//$this->redirect("this");
	}

	public function renderDefault()
	{

	}
	
	public function beforeRender()
	{
		parent::beforeRender();
		$this->template->installationMode = true;
		$this->template->hideMenuItems = true;
		
		$this->setTitle("Venne:CMS | Installation");
		$this->setKeywords("installation");
		$this->setDescription("Installation");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}
