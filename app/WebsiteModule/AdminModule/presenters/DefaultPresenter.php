<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace WebsiteModule\AdminModule;

use \Nette\Application\UI\Form;

/**
 * @author Josef Kříž
 * 
 * @resource AdminModule\WebsiteModule
 */
class DefaultPresenter extends \Venne\Developer\Presenter\AdminPresenter {


	/** @persistent */
	public $id;


	public function startup()
	{
		parent::startup();
		$this->addPath("Website setting", $this->link(":Website:Admin:Default:", array("id" => NULL)));
	}

	/**
	 * @allowed(administration-websites-edit)
	 */
	public function actionCreate()
	{
		$this->addPath("new item", $this->link(":Website:Admin:Default:create"));
	}


	/**
	 * @allowed(administration-websites-edit)
	 */
	public function actionEdit()
	{
		$this->addPath("edit" . " (" . $this->id . ")", $this->link(":Website:Admin:Default:edit"));
	}


	public function createComponentForm($name)
	{
		$form = new \Venne\Modules\WebsiteForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Website has been created");
		$form->setSubmitLabel(" Create");
		return $form;
	}


	public function createComponentFormEdit($name)
	{
		$form = new \Venne\Modules\WebsiteForm($this, $name);
		$form->setEntity($this->getWebsite()->getRepository()->find($this->id));
		$form->setSuccessLink("this");
		$form->setFlashMessage("Website has been updated");
		$form->setSubmitLabel(" Update");
		return $form;
	}

	public function beforeRender()
	{
		parent::beforeRender();
		$this->setTitle("Venne:CMS | Websites administration");
		$this->setKeywords("websites administration");
		$this->setDescription("Websites administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}
	
	/**
	 * @allowed(administration-websites-edit)
	 */
	public function handleDelete($id)
	{
		$this->getContext()->cms->website->remove($this->getContext()->cms->website->getRepository()->find($id));
		$this->flashMessage("Website has been deleted", "success");
		$this->redirect("this");
	}


	public function renderDefault()
	{
		$this->template->table = $this->context->services->website->getRepository()->findAll();
	}


	public function renderEdit()
	{
		
	}

}
