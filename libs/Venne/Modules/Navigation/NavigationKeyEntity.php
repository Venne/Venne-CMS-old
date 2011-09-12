<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\NavigationModule;

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 * @Entity(repositoryClass="\Venne\Developer\Doctrine\BaseRepository")
 * @Table(name="navigationKey")
 * 
 * @property string $val
 * @property string $key
 * @property /Venne/CMS/Models/Navigation $navigation
 */
class NavigationKeyEntity extends \Venne\Developer\Doctrine\BaseEntity{
	
	/**
	 *  @Column(type="string")
	 */
	protected $val;
	
	/**
	 *  @Column(name="`key`", type="string")
	 */
	protected $key;
	
	/**
	 * @ManyToOne(targetEntity="navigationEntity", inversedBy="id")
	 * @JoinColumn(name="navigation_id", referencedColumnName="id")
	 */
	protected $navigation;
	
	public function __toString()
	{
		return $this->val;
	}

}
