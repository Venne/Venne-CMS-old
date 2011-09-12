<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\SecurityModule;

use Venne;

/**
 * @author Josef Kříž
 */
class RoleService extends \Venne\Developer\Service\DoctrineService {


	public $entityNamespace = "\\Venne\\SecurityModule\\";

	/** @var \Venne\Application\Container */
	protected $context;


	public function __construct($context, $moduleName, \Doctrine\ORM\EntityManager $entityManager)
	{
		$this->context = $context;
		parent::__construct($moduleName, $entityManager);
	}


	/**
	 * @param bool $without
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
			$menu = $em->createQuery('SELECT u FROM \Venne\SecurityModule\RoleEntity u WHERE u.parent IS NULL')
					->getResult();
		else
			$menu = $em->createQuery('SELECT u FROM \Venne\SecurityModule\RoleEntity u WHERE u.parent= :depend ')
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
	 * Save structure
	 * @param array $data
	 */
	public function setStructure($data)
	{
		foreach ($data as $item) {
			foreach ($item as $item2) {
				$entity = $this->getRepository()->find($item2["id"]);
				$entity->parent = $this->getRepository()->find($item2["role_id"]);
			}
		}
		$this->getEntityManager()->flush();
	}

}
