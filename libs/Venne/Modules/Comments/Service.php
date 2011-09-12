<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CommentsModule;

use Venne;

/**
 * @author Josef Kříž
 */
class Service extends Venne\Developer\Service\DoctrineService {

	public $entityNamespace = "\\Venne\\CommentsModule\\";

	/** @var \Venne\Application\Container */
	protected $context;


	public function __construct($context, $moduleName, \Doctrine\ORM\EntityManager $entityManager)
	{
		$this->context = $context;
		parent::__construct($moduleName, $entityManager);
	}
	
	/**
	 * @param integer $moduleItemId
	 * @param string $moduleName
	 * @param bool $allow 
	 */
	public function saveSetting($moduleItemId, $moduleName, $allow)
	{
		$item = $this->getRepository()->findOneBy(
						array(
							"moduleItemId" => $moduleItemId,
							"moduleName" => $moduleName,
						)
		);
		if(!$item && $allow){
			$entity = $this->create();
			$entity->moduleName = $moduleName;
			$entity->moduleItemId = $moduleItemId;
			$this->getEntityManager()->persist($entity);
			$this->getEntityManager()->flush();
		}else if($item && !$allow){
			$this->getEntityManager()->remove($item);
			$this->getEntityManager()->flush();
		}
	}
	
	/**
	 * @param integer $moduleItemId
	 * @param string $moduleName
	 * @return bool
	 */
	public function getSetting($moduleItemId, $moduleName)
	{
		$item = $this->getRepository()->findOneBy(
						array(
							"moduleItemId" => $moduleItemId,
							"moduleName" => $moduleName,
						));
		return (bool)$item;
	}


}

