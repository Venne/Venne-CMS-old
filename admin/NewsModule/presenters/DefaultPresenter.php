<?php

namespace NewsModule;

use Nette\Forms\Form;
use Nette\Web\Html;

/**
 * @allowed("administration-pages")
 */
class DefaultPresenter extends \Venne\CMS\Developer\Presenter\AdminPresenter {


	/** @persistent */
	public $id;

	public function startup()
	{
		parent::startup();
		$this->addPath("News", $this->link(":News:Default:"));
	}

	/**
	 * @allowed(administration-pages-edit)
	 */
	public function actionCreate()
	{
		$this->addPath("new item", $this->link(":News:Default:create"));
	}

	/**
	 * @allowed(administration-pages-edit)
	 */
	public function actionEdit()
	{
		$this->addPath("Edit ({$this->id})", $this->link(":Pages:Default:edit"));
	}


	public function createComponentForm($name)
	{
		$form = new \Venne\CMS\Modules\NewsForm($this, $name);
		$form->setSuccessLink("default");
		$form->setFlashMessage("News has been created");
		$form->setSubmitLabel("Create");
		return $form;
	}


	public function createComponentFormEdit($name)
	{
		$form = new \Venne\CMS\Modules\NewsForm($this, $name, $this->getParam("id"));
		$form->setSuccessLink("this");
		$form->setFlashMessage("News has been updated");
		$form->setSubmitLabel("Update");
		return $form;
	}

	public function beforeRender()
	{
		parent::beforeRender();
		$this->setTitle("Venne:CMS | Pages administration");
		$this->setKeywords("pages administration");
		$this->setDescription("pages administration");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}


/**
	 * @allowed(administration-pages-edit)
	 */
	public function handleDelete($id)
	{
		$this->getModel()->removeItem($id);
		$this->flashMessage("News has been deleted", "success");
		$this->redirect("this", array("id" => NULL));
	}


	public function renderDefault()
	{
		$this->template->table = $this->getModel()->getItems();
	}

}