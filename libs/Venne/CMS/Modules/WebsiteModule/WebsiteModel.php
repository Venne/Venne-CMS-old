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
class WebsiteModel extends Venne\CMS\Developer\Model {

	/**
	 * @param Website $entity
	 * @param string $name
	 * @param string $regex
	 * @param string $template
	 * @param string $langType
	 * @param string $langValue
	 * @param integer $langDefault
	 * @param string $routePrefix
	 * @return Website 
	 */
	public function saveItem($entity, $name, $regex, $template, $langType, $langValue, $langDefault, $routePrefix)
	{
		if(!$entity){
			$entity = new Website;
			$this->getEntityManager()->persist($entity);
		}
		$entity->name = $name;
		$entity->regex = $regex;
		$entity->template = $template;
		$entity->langType = $langType;
		$entity->langValue = $langValue;
		$entity->langDefault = $langDefault;
		$entity->routePrefix = $routePrefix;
		
		$this->getEntityManager()->flush();
		return $entity;
	}
	
	/**
	 * @param \Nette\Forms\IControl $control
	 * @return bool 
	 */
	public function isNameAvailable(\Nette\Forms\IControl $control)
	{
		$name = $control->getValue();
		$entity = $control->parent->getEntity();
		$res = $this->getRepository()->findOneBy(array("name" => $name));
		if (!$res || ($res && $entity && $res->id == $entity->id)) {
			return true;
		}
		return false;
	}

}

