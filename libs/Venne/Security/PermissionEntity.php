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

/**
 * @author Josef Kříž
 * @Entity(repositoryClass="\Venne\Developer\Doctrine\BaseRepository")
 * @Table(name="permission")
 * 
 * @property string $resource
 * @property string $privilege
 * @property bool $allow
 * @property \Venne\Modules\Role $role
 */
class PermissionEntity extends \Venne\Developer\Doctrine\BaseEntity {


	/**
	 * @Column(type="string")
	 */
	protected $resource;

	/**
	 * @Column(type="string")
	 */
	protected $privilege;

	/**
	 * @Column(type="boolean")
	 */
	protected $allow;

	/**
	 * @ManyToOne(targetEntity="roleEntity", inversedBy="id")
	 * @JoinColumn(name="role_id", referencedColumnName="id")
	 */
	protected $role;

}
