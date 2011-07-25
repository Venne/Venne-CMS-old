<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace WebsModule;

use \Nette\Application\UI\Form;

/**
 * @author Josef Kříž
 * @allowed(administration-websites)
 */
class DefaultPresenter extends \Venne\CMS\Developer\Presenter\AdminPresenter {


	/** @persistent */
	public $id;


	public function startup()
	{
		parent::startup();
		$this->getNavigation()->addPath("Websites setting", $this->link(":Webs:Default:", array("id" => NULL)));
	}

	/**
	 * @allowed(administration-websites-edit)
	 */
	public function actionCreate()
	{
		$this->getNavigation()->addPath("new item", $this->link(":Webs:Default:create"));
	}


	/**
	 * @allowed(administration-websites-edit)
	 */
	public function actionEdit()
	{
		$this->getNavigation()->addPath("edit" . " (" . $this->id . ")", $this->link(":Webs:Default:edit"));
	}


	public function createComponentForm($name)
	{
		$form = new \Venne\CMS\Modules\WebsiteForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Website has been created");
		$form->addSubmit("submit", " Create");
		return $form;
	}


	public function createComponentFormEdit($name)
	{
		$form = new \Venne\CMS\Modules\WebsiteForm($this, $name);
		$form->setEntity($this->getWebsite()->getRepository()->find($this->id));
		$form->setSuccessLink("this");
		$form->setFlashMessage("Website has been updated");
		$form->addSubmit("submit", " Update");
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
		$this->getContext()->website->remove($this->getContext()->website->getRepository()->find($id));
		$this->flashMessage("Website has been deleted", "success");
		$this->redirect("this");
	}


	public function renderDefault()
	{
		$this->template->table = $this->getWebsite()->getRepository()->findAll();
	}


	public function renderEdit()
	{
		
	}

}
