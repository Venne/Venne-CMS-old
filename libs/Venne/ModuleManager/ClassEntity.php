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
 * @Table(name="moduleClass")
 */
class classEntity extends \Venne\Developer\Doctrine\BaseEntity {


	const TYPE_SERVICE = "service";
	const TYPE_ROUTE = "route";

	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $type;

	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $name;

	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $className;

	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected $dependencies;

	/**
	 * @ManyToOne(targetEntity="moduleEntity", inversedBy="id")
	 * @JoinColumn(name="module_id", referencedColumnName="id")
	 */
	protected $module;
	
	/**
	 * @OneToMany(targetEntity="KeyEntity", mappedBy="class", indexBy="key", cascade={"persist", "remove", "detach"})
	 */
	protected $keys;


	public function __construct($type, $moduleEntity, $name, $className, $dependencies)
	{
		$this->type = $type;
		$this->module = $moduleEntity;
		$this->dependencies = $dependencies;
		$this->className = $className;
		$this->name = $name;
	}


	public function getDependencies()
	{
		if(!$this->dependencies){
			return array();
		}
		return explode(";", $this->dependencies);
	}
	
	public function addKey($key, $val)
	{
		$this->keys[$key] = new KeyEntity($this, $key, $val);
	}

}
