<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\ModuleManager;

use Venne,
	Nette\Application\Routers\SimpleRouter,
	Nette\Application\Routers\Route;

/**
 * @author Josef Kříž
 */
class Manager {


	/** @var Nette\DI\Container */
	protected $container;
	protected $entityManager;

	/** @var \Nette\Caching\Cache */
	protected $cache;

	/** @var array */
	protected $classes = array();

	/** @var \Venne\ModuleManager\Service */
	protected $service;


	public function __construct($container, $entityManager)
	{
		$this->container = $container;
		$this->entityManager = $entityManager;
		$this->service = new Service("module", $this->container->doctrineContainer->entityManager);
	}


	/**
	 * @param string $moduleClass
	 * @return bool 
	 */
	public function installModule($moduleClass)
	{
		$module = new $moduleClass;
		$entity = $this->service->create(array(
			"moduleClass" => $moduleClass,
			"name" => $module->getName(),
			"description" => $module->getDescription(),
			"version" => $module->getVersion()
				)
		);

		$module->install($entity);

		$this->entityManager->persist($entity);
		dump($entity->classes[2]);
		$this->entityManager->flush();

		$this->cache->clean(array(
			\Nette\Caching\Cache::TAGS => array("Venne.ModelManager.config")
		));

		return true;
	}


	/**
	 * @param string $moduleClass
	 * @return bool 
	 */
	public function reinstallModule($moduleClass)
	{
		$this->uninstallModule($moduleClass);
		$this->installModule($moduleClass);

		return true;
	}


	/**
	 * @param string $moduleClass
	 * @return bool 
	 */
	public function uninstallModule($moduleClass)
	{
		$entity = $this->entityManager->getRepository("\Venne\ModuleManager\ModuleEntity")->findOneBy(array("moduleClass" => $moduleClass));
		$this->entityManager->remove($entity);
		$this->entityManager->flush();

		$this->cache->clean(array(
			\Nette\Caching\Cache::TAGS => array("Venne.ModelManager.config")
		));

		return true;
	}


	protected function moduleCode($moduleId)
	{
		$container = $this->container;

		$code = "";

		$classes = $this->entityManager->getRepository("\Venne\ModuleManager\ClassEntity")->findBy(array("module" => $moduleId));
		foreach ($classes as $item) {
			$args = array();
			//dump($item->dependencies);
			foreach ($item->dependencies as $dependency) {
				if(substr($dependency, 0, 1) == "@"){
					$dependency = substr($dependency, 1);
					if($dependency == "entityManager"){
						$args[] = "\$container->context->doctrineContainer->getService('$dependency')";
					}else{
						$args[] = "\$container->getService('$dependency')";
					}
				}else{
					$args[] = "'$dependency'";
				}
				
			}
			$args = join(", ", $args);

		
			$code .= "\$container->{$item->type}s->addService('{$item->name}', function(\$container) {\n";
			$code .= "	\$class = '{$item->className}';\n";
			$code .= "	\$service = new \$class($args);\n";
			$code .= "	return \$service;\n";
			$code .= "}, array ( 0 => 'run', ));\n\n";
		}
		//die($code);
		return $code;
	}


	public function getClasses()
	{
		return $this->entityManager->getRepository("\Venne\ModuleManager\ClassEntity")->findAll();
	}


	public function getClassesByType($type)
	{
		return $this->entityManager->getRepository("\Venne\ModuleManager\ClassEntity")->findBy(array("type" => $type));
	}

}
