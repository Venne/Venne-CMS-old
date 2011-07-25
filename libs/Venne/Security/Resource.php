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
 * @Table(name="resource")
 * 
 * @property string $name
 */
class Resource extends \Venne\Models\BaseEntity {

	/**
	 * @Column(type="string")
	 */
	protected $name;
	/**
	 * @OneToMany(targetEntity="resource", mappedBy="parent")
	 */
	protected $childrens;
	/**
	 * @ManyToOne(targetEntity="resource", inversedBy="id")
	 * @JoinColumn(name="resource_id", referencedColumnName="id")
	 */
	protected $parent;
	/**
	 * @OneToMany(targetEntity="permission", mappedBy="resource")
	 */
	protected $permissions;
	/**
	 * @OneToMany(targetEntity="privilege", mappedBy="resource")
	 */
	protected $privileges;
	
}
