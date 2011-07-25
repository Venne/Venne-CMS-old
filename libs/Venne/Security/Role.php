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
 * @Entity(repositoryClass="Venne\CMS\Modules\RoleRepository")
 * @Table(name="role")
 * 
 * @property string $name
 */
class Role extends \Venne\Models\BaseEntity {

	/**
	 * @Column(type="string")
	 */
	protected $name;
	/**
	 * @OneToMany(targetEntity="role", mappedBy="parent")
	 */
	protected $childrens;
	/**
	 * @ManyToOne(targetEntity="role", inversedBy="id")
	 * @JoinColumn(name="role_id", referencedColumnName="id")
	 * @OrderBy({"order" = "ASC"})
	 */
	protected $parent;
	/**
	 * @OneToMany(targetEntity="permission", mappedBy="role")
	 */
	protected $permissions;
	
	public function __construct() {
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
    }

	
}
