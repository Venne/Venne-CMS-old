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
class NavigationService
extends BaseService
implements
	\Venne\CMS\Developer\IContentExtensionModule	
{


	/** @var string */
	protected $className = "navigation";
	/** @var array() */
	protected $path = array();
	/** @var \Venne\CMS\Modules\Navigation */
	protected $currentNavigation;
	/** @var \Venne\CMS\Modules\Navigation */
	protected $currentFrontNavigation;


	/**
	 * @param \Nette\Http\Request $httpRequest
	 * @return \Venne\CMS\Modules\Navigation
	 */
	public function getCurrentNavigation($httpRequest)
	{
		if (!isset($this->currentNavigation)) {
			$repo = $this->getRepository();

			$website = $this->getContainer()->website->getCurrentWebsite($httpRequest);
			$this->currentNavigation = $repo->findBy(array("website" => $website->id, "parent" => NULL));
		}
		return $this->currentNavigation;
	}


	/**
	 * @param \Nette\Http\Request $httpRequest
	 * @return \Venne\CMS\Modules\Navigation
	 */
	public function getCurrentFrontNavigation($httpRequest)
	{
		if (!isset($this->currentNavigation)) {
			$repo = $this->getRepository();

			$website = $this->getContainer()->website->getCurrentFrontWebsite($httpRequest);
			$this->currentFrontNavigation = $repo->findBy(array("website" => $website->id, "parent" => NULL));
		}
		return $this->currentFrontNavigation;
	}


	/**
	 * @param \Nette\Http\Request $httpRequest
	 * @param bool $without
	 * @param int $layer
	 * @param int $depend
	 * @return array
	 */
	public function getCurrentFrontList($httpRequest, $without = Null, $layer = 0, $depend = Null)
	{
		$em = $this->getEntityManager();
		$data = array();
		$text = "";
		if (!$depend)
			$menu = $em->createQuery('SELECT u FROM \Venne\CMS\Modules\Navigation u WHERE u.parent IS NULL AND u.website = :website')
					->setParameter("website", $this->getContainer()->website->getCurrentFrontWebsite($httpRequest)->id)
					->getResult();
		else
			$menu = $em->createQuery('SELECT u FROM \Venne\CMS\Modules\Navigation u WHERE u.parent= :depend ')
					->setParameters(array("depend" => $depend))
					->getResult();
		for ($i = 0; $i <= $layer; $i++) {
			$text .= "--";
		}
		foreach ($menu as $item) {
			if ($item->id != $without) {
				$data[$item->id] = $text . "- " . $item->name;
				$data += $this->getList($without, $layer + 1, $item->id);
			}
		}
		return $data;
	}


	/**
	 * @param \Nette\Http\Request $httpRequest
	 * @param int $layer
	 * @param int $depend
	 * @return array
	 */
	public function getList($without = Null, $layer = 0, $depend = Null)
	{
		$em = $this->getEntityManager();
		$data = array();
		$text = "";
		if (!$depend)
			$menu = $em->createQuery('SELECT u FROM \Venne\CMS\Modules\Navigation u WHERE u.parent IS NULL')->getResult();
		else
			$menu = $em->createQuery('SELECT u FROM \Venne\CMS\Modules\Navigation u WHERE u.parent= :depend ')
					->setParameters(array("depend" => $depend))
					->getResult();
		for ($i = 0; $i <= $layer; $i++) {
			$text .= "--";
		}
		foreach ($menu as $item) {
			if ($item->id != $without) {
				$data[$item->id] = $text . "- " . $item->name;
				$data += $this->getList($without, $layer + 1, $item->id);
			}
		}
		return $data;
	}


	/**
	 * @param string $name
	 * @param string $url 
	 */
	public function addPath($name, $url)
	{
		$data = new Venne\CMS\Navigation\PathItem;
		$data->setName($name);
		$data->setUrl($url);
		$this->path[] = $data;
	}


	/**
	 * @return array 
	 */
	public function getPaths()
	{
		return $this->path;
	}


	/**
	 * Save structure
	 * @param array $data
	 */
	public function setStructure($data)
	{
		foreach ($data as $item) {
			foreach ($item as $item2) {
				$entity = $this->getRepository()->find($item2["id"]);
				$entity->parent = $this->getRepository()->find($item2["navigation_id"]);
				$entity->order = $item2["order"];
			}
		}
		$this->getEntityManager()->flush();
	}


	public function getContentExtension()
	{
		return new NavigationContentExtension($this->container);
	}


}

