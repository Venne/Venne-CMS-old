<?php

namespace PagesModule;

use Nette\Environment;

/**
 * @allowed(module-pages)
 */
class DefaultPresenter extends \Venne\CMS\Developer\Presenter\FrontPresenter {


	/** @persistent */
	public $url = "";


	public function startup()
	{
		parent::startup();

		$this->template->entity = $this->getEntityManager()->getRepository("\\Venne\\CMS\\Modules\\Pages")->findOneBy(array("url" => $this->url, "website" => $this->getWebsite()->current->id));

		if (!$this->template->entity && !$this->url) {
			$this->template->entity = $this->getEntityManager()->getRepository("\\Venne\\CMS\\Modules\\Pages")->findOneBy(array("mainPage" => true, "website" => $this->getWebsite()->current->id));
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