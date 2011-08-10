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
 * @property PagesService $parent
 */
class PagesModel extends Venne\CMS\Developer\Model {


	/**
	 * @param Pages $entity
	 * @param string $title
	 * @param string $url
	 * @param string $text
	 * @param bool $mainPage
	 * @param string $keywords
	 * @param string $description
	 * @param DateTime $created
	 * @param DateTime $updated
	 * @param Website $websiteEntity
	 * @return Pages 
	 */
	public function saveItem($entity, $title, $url, $text, $mainPage = false, $keywords = NULL, $description = NULL, $created = NULL, $updated = NULL, $websiteEntity = NULL)
	{
		if (!$websiteEntity)
			$websiteEntity = $this->container->website->currentFront;

		if (!$entity) {
			$entity = new Pages;
			$this->getEntityManager()->persist($entity);
		}
		$entity->title = $title;
		$entity->url = $url;
		$entity->text = $text;
		$entity->keywords = $keywords;
		$entity->description = $description;
		$entity->created = $created;
		$entity->updated = $updated;
		$entity->website = $websiteEntity;

		/* set main Page */
		if ($mainPage) {
			foreach ($this->getRepository()->findByWebsite($websiteEntity->id) as $page) {
				$page->mainPage = false;
			}
			$entity->mainPage = true;
		}

		$this->getEntityManager()->flush();
		return $entity;
	}


	/**
	 * @param integer $id 
	 */
	public function removeItem($id)
	{
		$item = $this->getRepository()->find($id);
		$this->getEntityManager()->remove($item);
		$this->getEntityManager()->flush();
		
		$this->parent->onRemoveItem("pages", $id);
	}


	/**
	 * @param \Nette\Forms\IControl $control
	 * @return bool 
	 */
	public function isUrlAvailable(\Nette\Forms\IControl $control)
	{
		$url = $control->getValue();
		$entity = $control->parent->getEntity();
		$res = $this->getRepository()->findOneBy(array("url" => $url));
		if (!$res || ($res && $entity && $res->id == $entity->id)) {
			return true;
		}
		return false;
	}

}

