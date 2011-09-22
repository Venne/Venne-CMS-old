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
class UserService extends \Venne\Developer\Service\DoctrineService {


	public $entityNamespace = "\\Venne\\SecurityModule\\";

	/** @var \Venne\Application\Container */
	protected $context;


	public function __construct($context, $moduleName, \Doctrine\ORM\EntityManager $entityManager)
	{
		$this->context = $context;
		parent::__construct($moduleName, $entityManager);
	}


	public function create($values = array(), $withoutFlush = false)
	{
		if (!array_key_exists("salt", $values)) {
			$values["salt"] = \Nette\Utils\Strings::random(8);
		}
		if (!array_key_exists("password", $values)) {
			$values["password"] = md5($values["salt"] . $values["password"]);
		}
		if (!array_key_exists("enable", $values)) {
			$values["enable"] = 1;
		}
		parent::create($values, $withoutFlush);
	}

}
