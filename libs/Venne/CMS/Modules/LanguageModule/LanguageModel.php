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
class LanguageModel extends Venne\CMS\Developer\Model {
	
	/**
	 * @param Language $entity
	 * @param string $lang
	 * @param string $name
	 * @param string $alias
	 * @param Website $websiteEntity
	 * @return Language
	 */
	public function saveItem($entity, $lang, $name, $alias, $websiteEntity = NULL)
	{
		if(!$websiteEntity){
			if($this->container->params['venneModeInstallation']){
				$websiteEntity = $this->container->website->getRepository()->find(-1);
			}else{
				$websiteEntity = $this->container->website->currentFront;
			}
		}
		
		if(!$entity){
			$entity = new Language;
			$this->getEntityManager()->persist($entity);
		}
		$entity->lang = $lang;
		$entity->name = $name;
		$entity->alias = $alias;
		$entity->website = $websiteEntity;
		
		$this->getEntityManager()->flush();
		return $entity;
	}
	
}