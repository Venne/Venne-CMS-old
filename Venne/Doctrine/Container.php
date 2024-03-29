<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Doctrine;

use Venne;

/**
 * @author Josef Kříž
 * 
 * @property-read Doctrine\ORM\EntityManager $entityManager
 * @property-read Doctrine\DBAL\Schema\SchemaManager $echemaManager
 */
class Container extends \Nette\DI\Container {


	/** @var \Nette\DI\Container */
	private $context;


	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(\Nette\DI\Container $context)
	{
		$this->context = $context;
	}

	/**
	 * @param \Nette\DI\Container
	 * @return \Doctrine\Common\EventManager
	 */
	public function createServiceEventManager()
	{
		$evm = new \Doctrine\Common\EventManager;
		foreach (array_keys($this->context->getServiceNamesByTag('listener')) as $name) {
			$evm->addEventSubscriber($this->context->getService($name));
		}
		return $evm;
	}

	/**
	 * @param \Nette\DI\IContainer
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function createServiceEntityManager()
	{
		$evm = $this->eventManager;
		
		if (!$this->context->params["productionMode"]) {
		    $cache = new \Doctrine\Common\Cache\ArrayCache;
		} else {
			$cache = new \Doctrine\Common\Cache\ApcCache();
		}

		$config = new \Doctrine\ORM\Configuration();
		$config->setMetadataCacheImpl($cache);
		$driverImpl = $config->newDefaultAnnotationDriver(array($this->context->params["appDir"], $this->context->params["venneDir"]));
		$config->setMetadataDriverImpl($driverImpl);
		$config->setQueryCacheImpl($cache);
		$config->setProxyDir($this->context->params["appDir"] . '/proxies');
		$config->setProxyNamespace('App\Proxies');

		//if ($applicationMode == "development") {
		$config->setAutoGenerateProxyClasses(true);
		//} else {
		//	$config->setAutoGenerateProxyClasses(false);
		//}
		return \Doctrine\ORM\EntityManager::create((array) $this->context->params['database'], $config, $evm);
	}


	/**
	 * @param \Nette\DI\IContainer
	 * @return \Doctrine\DBAL\Schema\SchemaManager
	 */
	public function createServiceSchemaManager()
	{
		$db = \Doctrine\DBAL\DriverManager::getConnection($this->context->params['database']);
		return $db->getSchemaManager();
	}

}