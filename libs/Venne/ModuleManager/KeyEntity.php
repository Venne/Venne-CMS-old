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
 * @Entity
 * @Table(name="moduleClassKey")
 */
class KeyEntity extends \Venne\Developer\Doctrine\BaseEntity {

	/**
	 * @Column(type="string", name="`key`")
	 */
	protected $key;
	
	/**
	 * @Column(type="string")
	 */
	protected $val;
	
	/**
	 * @ManyToOne(targetEntity="classEntity", inversedBy="id")
	 * @JoinColumn(name="moduleClass_id", referencedColumnName="id")
	 */
	protected $class;


	public function __construct($moduleClassEntity, $key, $val)
	{
		$this->class = $moduleClassEntity;
		$this->key = $key;
		$this->val = $val;
	}
		
}
