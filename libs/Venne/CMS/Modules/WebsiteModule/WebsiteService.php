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
class WebsiteService extends BaseService {


	/** @var string */
	protected $className = "website";
	/** @var \Venne\CMS\Modules\Website */
	protected $currentWebsite;
	/** @var \Venne\CMS\Modules\Website */
	protected $currentFrontWebsite;


	/**
	 * @param \Nette\Http\Request $httpRequest
	 * @return \Venne\CMS\Modules\Website
	 */
	public function getCurrentWebsite(\Nette\Http\Request $httpRequest)
	{
		$repo = $this->getRepository();

		if (!$this->currentWebsite) {
			if (defined('VENNE_MODE_ADMIN')) {
				$this->currentWebsite = $repo->findOneBy(array("name" => "admin"));
			} else if (defined('VENNE_MODE_FRONT')) {
				$websites = $repo->findAll();
				foreach ($websites as $item) {
					if ($item->name == "admin")
						continue;
					$reg = "/^" . str_replace("*", ".*", str_replace("/", "\/", $item->regex)) . "$/";
					if (preg_match($reg, $httpRequest->getUrl()->getBaseUrl())) {
						$this->currentWebsite = $item;
						break;
					}
				}
			} else {
				$this->currentWebsite = new Website;
				$this->currentWebsite->name = "installation";
				$this->currentWebsite->template = "admin";
			}
			if(!$this->currentWebsite){
				throw new InvalidWebsiteException("Website does not exist");
			}
		}
		return $this->currentWebsite;
	}


	/**
	 * @param \Nette\Http\Request $httpRequest
	 * @return \Venne\CMS\Modules\Website
	 */
	public function getCurrentFrontWebsite(\Nette\Http\Request $httpRequest)
	{
		$repo = $this->getRepository();

		if (!$this->currentFrontWebsite) {
			$webId = $httpRequest->getQuery("webId");
			if (!$webId) {
				foreach ($repo->findAll() as $web) {
					if ($web->name == "admin")
						continue;
					$webId = $web->id;
					break;
				}
			}
			$this->currentFrontWebsite = $repo->find($webId);
			if(!$this->currentFrontWebsite){
				throw new InvalidWebsiteException("Website does not exist");
			}
		}
		return $this->currentFrontWebsite;
	}

}
