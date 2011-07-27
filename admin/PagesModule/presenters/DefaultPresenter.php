<?php

namespace PagesModule;

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
		$this->addPath("Pages", $this->link(":Pages:Default:"));
	}

	/**
	 * @allowed(administration-pages-edit)
	 */
	public function actionCreate()
	{
		$this->addPath("new item", $this->link(":Navigation:Default:create"));
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
		$form = new \Venne\CMS\Modules\PagesForm($this, $name, "\Venne\CMS\Modules\Pages");
		$form->setSuccessLink("default");
		$form->setFlashMessage("Page has been created");
		$form->addSubmit("submit", "Create");
		return $form;
	}


	public function createComponentFormEdit($name)
	{
		$form = new \Venne\CMS\Modules\PagesForm($this, $name, "\Venne\CMS\Modules\Pages");
		$form->setSuccessLink("this");
		$form->setEntity($this->getEntityManager()->getRepository("\\Venne\\CMS\\Modules\\Pages")->find($this->id));
		$form->setFlashMessage("Page has been updated");
		$form->addSubmit("submit", "Update");
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
		$item = $this->getEntityManager()->getRepository("\\Venne\\CMS\\Modules\\Pages")->find($id);
		$this->getEntityManager()->remove($item);
		$this->getEntityManager()->flush();
		$this->flashMessage("Page has been deleted", "success");
		$this->redirect("this", array("id" => NULL));
	}


	public function renderDefault()
	{
		$this->template->table = $this->getEntityManager()->getRepository("\\Venne\\CMS\\Modules\\Pages")->findByWebsite($this->getWebsite()->currentFront->id);
	}

}