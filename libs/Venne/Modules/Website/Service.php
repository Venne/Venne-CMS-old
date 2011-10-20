<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\WebsiteModule;

use Venne;

/**
 * @author Josef Kříž
 */
class Service extends \Venne\Developer\Service\DoctrineService {


	public $entityNamespace = "\\Venne\\WebsiteModule\\";
	protected $currentWebsite;
	protected $currentFrontWebsite;

	/** @var \Venne\Application\Container */
	protected $context;


	public function __construct($context, $moduleName, \Doctrine\ORM\EntityManager $entityManager)
	{
		$this->context = $context;
		parent::__construct($moduleName, $entityManager);
	}


	/**
	 * @return \Venne\Modules\WebsiteEntity
	 */
	public function getCurrent()
	{
		$currentWebsite = new WebsiteEntity;
		if ($this->context->params["venneModeFront"]) {
			$currentWebsite->name = "test";
			$currentWebsite->skin = $this->context->params["website"]["theme"];
			return $currentWebsite;
		} else {
			$currentWebsite->name = "test";
			$currentWebsite->skin = "admin";
		}
		return $currentWebsite;
	}


	/**
	 * @return \Venne\Modules\WebsiteEntity
	 */
	public function getCurrentFront()
	{
		$repo = $this->getRepository();

		$webId = $this->context->httpRequest->getQuery("webId");
		if (!$webId) {
			foreach ($repo->findAll() as $web) {
				if ($web->name == "admin")
					continue;
				$webId = $web->id;
				break;
			}
		}
		$currentFrontWebsite = $repo->find($webId);
		if (!isset($currentFrontWebsite) || !$currentFrontWebsite) {
			throw new InvalidWebsiteException("Website does not exist");
		}
		return $currentFrontWebsite;
	}
	
	public function setTheme($name)
	{
		$config = \Nette\Config\NeonAdapter::load($this->context->params["appDir"] . '/config.neon');
		$config["common"]["website"]["theme"] = $name;
		$config["development"]["website"]["theme"] = $name;
		$config["production"]["website"]["theme"] = $name;
		$config["console"]["website"]["theme"] = $name;
		\Venne\Config\NeonAdapter::save($config, $this->context->params["appDir"] . '/config.neon', "common", array("production", "development", "console"));

	}

}
