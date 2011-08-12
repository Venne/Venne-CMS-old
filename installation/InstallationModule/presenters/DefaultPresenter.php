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
		$paths = array(EXTENSIONS_DIR, WWW_DIR . "/public/", WWW_DIR . "/skins/", WWW_DIR . "/../config.neon", FLAGS_DIR);
		foreach ($paths as $item) {
			if (!is_writable($item)) {
				$this->flashMessage("Path " . $item . " is not writable.", "warning");
			}
		}
		
	}
	
	public function actionFinish()
	{
		$this->getContext()->system->model->setInstallationDone();
		$this->template->websiteUrl = $this->getHttpRequest()->getUrl()->getBaseUrl();
	}

	
	public function createComponentFormAccount($name)
	{
		$form = new \Venne\CMS\Modules\SystemAccountForm($this, $name, "common");
		$form->setSuccessLink("database");
		$form->setSubmitLabel("Next");
		return $form;
	}
	
	public function createComponentFormDatabase($name)
	{
		$form = new \Venne\CMS\Modules\SystemDatabaseForm($this, $name, "common", false, true);
		$form->setSuccessLink("website");
		//$form->setFlashMessage("Database settings has been updated");
		$form->setSubmitLabel("Install");
		return $form;
	}
	
	public function createComponentFormWebsite($name)
	{
		$form = new \Venne\CMS\Modules\WebsiteForm($this, $name);
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
		$this->template->installationMode = true;
		$this->template->hideMenuItems = true;
		
		$this->setTitle("Venne:CMS | Installation");
		$this->setKeywords("installation");
		$this->setDescription("Installation");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}
