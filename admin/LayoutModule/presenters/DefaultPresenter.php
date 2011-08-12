<?php

namespace LayoutModule;

use Nette\Utils\Html;

/**
 * @allowed(administration-navigation)
 */
class DefaultPresenter extends \Venne\CMS\Developer\Presenter\AdminPresenter {


	/** @persistent */
	public $key;


	public function startup()
	{
		parent::startup();

		$this->addPath("Layout", $this->link(":Layout:Default:"));
		$this->template->items = $this->getModel()->getLayouts();
	}
	
	public function actionCreate()
	{
		$this->addPath("New item", $this->link(":Layout:Default:create"));
	}


	public function actionEdit()
	{
		$this->addPath("edit" . " (" . $this->key . ")", $this->link(":Layout:Default:edit"));
	}
	
	public function createComponentForm($name)
	{
		$form = new \Venne\CMS\Modules\LayoutForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Layout has been saved");
		$form->setSubmitLabel("Create");
		return $form;
	}
	
	public function createComponentFormEdit($name)
	{
		$form = new \Venne\CMS\Modules\LayoutForm($this, $name, $this->getParam("key"));
		$form->setSuccessLink("this");
		$form->setFlashMessage("Repository has been updated");
		$form->setSubmitLabel("Update");
		return $form;
	}

	public function handleDelete($key)
	{
		$this->getModel()->removeLayout($key);
		$this->flashMessage("Layout has been deleted", "success");
		$this->redirect("this");
	}

	public function beforeRender()
	{
		parent::beforeRender();
		$this->setTitle("Venne:CMS | Layout administration");
		$this->setKeywords("layout administration");
		$this->setDescription("Layout administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}