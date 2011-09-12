<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\LayoutModule;

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 * @Entity
 * @Table(name="layout")
 * 
 * @property /Venne/CMS/Models/LayoutKey $keys
 * @property /Venne/CMS/Models/Website $website
 * @property string $regex
 * @property string $layout
 * @property string $moduleName
 * @property integer $moduleItemId
 */
class LayoutEntity extends \Venne\Developer\Doctrine\BaseEntity {


	/** @Column(name="`regex`", type="string", length=255) */
	protected $regex;

	/** @Column(type="string", length=255) */
	protected $moduleName;

	/** @Column(type="string", length=255) */
	protected $moduleItemId;

	/** @Column(type="string", length=255) */
	protected $layout;
	
	/**
	 * @OneToMany(targetEntity="layoutKeyEntity", mappedBy="layout", indexBy="key", cascade={"persist", "remove", "detach"})
	 */
	protected $keys;

}
