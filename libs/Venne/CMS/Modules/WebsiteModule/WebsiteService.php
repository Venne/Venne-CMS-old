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
class WebsiteService extends BaseService implements
\Venne\CMS\Developer\IModelModule {


	/** @var string */
	protected $className = "website";


	/**
	 * @return \Venne\CMS\Modules\Website
	 */
	public function createServiceCurrent()
	{
		$repo = $this->getRepository();

		if (defined('VENNE_MODE_ADMIN')) {
			return $repo->findOneBy(array("name" => "admin"));
		} else if (defined('VENNE_MODE_FRONT')) {
			$websites = $repo->findAll();
			foreach ($websites as $item) {
				if ($item->name == "admin")
					continue;
				$reg = "/^" . str_replace("*", ".*", str_replace("/", "\/", $item->regex)) . "$/";
				if (preg_match($reg, $this->container->httpRequest->getUrl()->getBaseUrl())) {
					return $item;
				}
			}
		} else {
			$currentWebsite = new Website;
			$currentWebsite->name = "installation";
			$currentWebsite->template = "admin";
		}
		if (!$currentWebsite) {
			throw new InvalidWebsiteException("Website does not exist");
		}
		return $currentWebsite;
	}


	/**
	 * @return \Venne\CMS\Modules\Website
	 */
	public function createServiceCurrentFront()
	{
		$repo = $this->getRepository();

		$webId = $this->container->httpRequest->getQuery("webId");
		if (!$webId) {
			foreach ($repo->findAll() as $web) {
				if ($web->name == "admin")
					continue;
				$webId = $web->id;
				break;
			}
		}
		$currentFrontWebsite = $repo->find($webId);
		if (!$currentFrontWebsite) {
			throw new InvalidWebsiteException("Website does not exist");
		}
		return $currentFrontWebsite;
	}


	/**
	 * @return WebsiteModel 
	 */
	public function createServiceModel()
	{
		return new WebsiteModel($this->container, $this);
	}

}
