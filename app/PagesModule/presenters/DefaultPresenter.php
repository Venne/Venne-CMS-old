<?php

namespace App\PagesModule;

use Nette\Environment;

/**
 * @resource PagesModule
 */
class DefaultPresenter extends \Venne\Developer\Presenter\FrontPresenter {


	/** @persistent */
	public $url = "";

	/**
	 * @privilege read
	 */
	public function startup()
	{
		parent::startup();

		$this->template->entity = $this->context->services->pages->getRepository()->findOneBy(array("url" => $this->url));

		if (!$this->template->entity && !$this->url) {
			$this->template->entity = $this->context->services->pages->getRepository()->findOneBy(array("mainPage" => true));
			if (!$this->template->entity) {
				throw new \Nette\Application\BadRequestException;
			}
			$this->url = $this->template->entity->url;
		}

		if (!$this->template->entity) {
			throw new \Nette\Application\BadRequestException;
		}

		$this->contentExtensionsKey = $this->template->entity->id;
	}
	
	public function createComponentForm($name)
	{
		$form = new \Venne\Modules\PagesFrontForm($this, $name);
		$form->setSuccessLink("this");
		$form->setFlashMessage("Page has been updated");
		return $form;
	}


	public function beforeRender()
	{
		parent::beforeRender();
		$entity = $this->template->entity;

		$this->setTitle($entity->title);
		$this->setKeywords($entity->keywords);
		$this->setDescription($entity->description);
		$this->setRobots(self::ROBOTS_INDEX | self::ROBOTS_FOLLOW);
	}

}