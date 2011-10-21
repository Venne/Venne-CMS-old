<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\PagesModule;

use Venne;

/**
 * @author Josef Kříž
 */
class Service extends \Venne\Developer\Service\DoctrineService {

	public $entityNamespace = "\\App\\PagesModule\\";

	/** @var array */
	public $onRemoveItem;
	
	/** @var \HookModule\Manager */
	protected $hookManager;

	public function __construct($moduleName, \Doctrine\ORM\EntityManager $entityManager, \App\HookModule\Manager $hookManager)
	{
		$this->hookManager = $hookManager;
		parent::__construct($moduleName, $entityManager);
	}
	
	public function getSitemap($priority)
	{
		$sitemap = new \Venne\CMS\Sitemap\Sitemap;

		$pages = $this->getRepository()->findByWebsite($this->website->current->id);

		$url = $this->container->httpRequest->getUrl();
		$prefix = $url->getScheme() . "://" . $url->getHost() . $url->getBasePath() . $this->params["modules"]["pagesModule"]["routePrefix"];

		foreach ($pages as $page) {
			$sitemap->addItem($prefix . $page->url, $page->updated->format('Y-m-d'), \Venne\CMS\Sitemap\Sitemap::CHANGE_WEEKLY, round(0.5 * $priority * 10) / 10);
		}

		return $sitemap->getXml();
	}


	/**
	 * @return PagesModel 
	 */
	public function createServiceModel()
	{
		return new PagesModel($this);
	}


	public function hookAdminMenu($menu)
	{
		$nav = new \App\NavigationModule\NavigationEntity("Pages module");
		$nav->addKey("module", "Pages:Admin");
		$menu[] = $nav;
	}


	public function getCallbacks()
	{
		return array(
			"onRemoveItem" => CallbackService::REMOVE_ITEM,
		);
	}
	
	public function delete(Venne\Developer\Doctrine\BaseEntity $entity, $withoutFlush = false)
	{
		$this->hookManager->callHook("content\\extension\\remove", "pages", $entity->id, array(
			"module"=>"Pages",
			"presenter"=>"Default",
			"action"=>"default",
			"url"=>$entity->url
			));
		dump($entity);
		parent::delete($entity, $withoutFlush);
	}
	
	/**
	 * @param \Nette\Forms\IControl $control
	 * @return bool 
	 */
	public function isUrlAvailable(\Nette\Forms\IControl $control)
	{
		$url = $control->getValue();
		$entity = $control->parent->key;
		$res = $this->getRepository()->findOneBy(array("url" => $url));
		if (!$res || ($res && $entity && $res->id == $entity->id)) {
			return true;
		}
		return false;
	}

}