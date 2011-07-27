<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Modules;

use Venne;

/**
 * @author Josef Kříž
 */
class PagesService extends BaseService implements
Venne\CMS\Developer\IRouteModule, Venne\CMS\Developer\ISitemapModule, Venne\CMS\Developer\IModelModule {


	protected $className = "pages";


	public function getCurrent($httpRequest)
	{
		if (!isset($this->currentNavigation)) {
			$repo = $this->getRepository();

			$website = $this->getContainer()->navigation->current($httpRequest);
			$this->currentNavigation = $repo->findBy(array("website" => $website->id, "parent" => NULL));
		}
		return $this->currentNavigation;
	}


	public function getRoute(\Nette\Application\Routers\RouteList $router, $values = array(), $prefix = "")
	{
		$router[] = new \Nette\Application\Routers\Route($prefix . '[[<url>]/]', $values + array(
					'module' => 'Pages',
					'presenter' => 'Default',
					'action' => 'default',
					'url' => array(
						\Nette\Application\Routers\Route::PATTERN => '.*?',
						\Nette\Application\Routers\Route::FILTER_IN => NULL,
						\Nette\Application\Routers\Route::FILTER_OUT => NULL
					)
						)
		);
	}


	public function getSitemap($priority)
	{
		$sitemap = new \Venne\CMS\Sitemap\Sitemap;

		$pages = $this->getRepository()->findByWebsite($this->container->navigation->current($this->container->httpRequest)->id);

		$url = $this->container->httpRequest->getUrl();
		$prefix = $url->getScheme() . "://" . $url->getHost() . $url->getBasePath() . $this->container->params["CMS"]["modules"]["pagesModule"]["routePrefix"];

		foreach ($pages as $page) {
			$sitemap->addItem($prefix . $page->url, $page->updated->format('Y-m-d'), \Venne\CMS\Sitemap\Sitemap::CHANGE_WEEKLY, round(0.5 * $priority * 10) / 10);
		}

		return $sitemap->getXml();
	}


	/**
	 * @return NavigationModel 
	 */
	public function createServiceModel()
	{
		return new PagesModel($this->container, $this);
	}

}

