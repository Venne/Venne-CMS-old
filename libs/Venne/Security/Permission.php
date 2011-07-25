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

/**
 * @author Josef Kříž
 * @Entity(repositoryClass="Venne\CMS\Modules\NavigationRepository")
 * @Table(name="permission")
 * 
 * @property \Venne\CMS\Modules\Resource $resource
 * @property string $privilege
 * @property bool $allow
 * @property \Venne\CMS\Modules\Role $role
 */
class Permission extends \Venne\Models\BaseEntity {


	/**
	 * @Column(type="string")
	 */
	protected $privilege;
	/**
	 * @Column(type="boolean")
	 */
	protected $allow;
	/**
	 * @ManyToOne(targetEntity="role", inversedBy="id")
	 * @JoinColumn(name="role_id", referencedColumnName="id")
	 */
	protected $role;
	/**
	 * @ManyToOne(targetEntity="resource", inversedBy="id")
	 * @JoinColumn(name="resource_id", referencedColumnName="id")
	 */
	protected $resource;

	public function __construct()
	{
		$this->privilege = "";
	}

}
