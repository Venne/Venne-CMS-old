<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Modules;

use Venne;

/**
 * @author Josef Kříž
 * @Entity
 * @Table(name="navigationLang")
 * 
 * @property /Venne/CMS/Models/Navigation $navigation
 * @property string $name
 * @property int $language
 */
class NavigationLang extends \Venne\Developer\Doctrine\BaseEntity {
	
	
	/**
	 * @ManyToOne(targetEntity="navigation", inversedBy="id")
	 * @JoinColumn(name="navigation_id", referencedColumnName="id")
	 */
	protected $navigation;
	
	/** @Column(name="language_id", type="integer") */
	protected $language;
	
	/** @Column(type="string", length=300) */
	protected $name;
	
}
