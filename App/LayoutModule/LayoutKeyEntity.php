<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\LayoutModule;

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 * @Entity
 * @Table(name="layoutKey")
 * 
 * @property string $val
 * @property string $key
 * @property /Venne/CMS/Models/Layout $layout
 */
class LayoutKeyEntity extends \Venne\Developer\Doctrine\BaseEntity{
	
	/**
	 *  @Column(type="string")
	 */
	protected $val;
	
	/**
	 *  @Column(name="`key`", type="string")
	 */
	protected $key;
	
	/**
	 * @ManyToOne(targetEntity="layoutEntity", inversedBy="id")
	 * @JoinColumn(name="layout_id", referencedColumnName="id", onDelete="CASCADE", onUpdate="CASCADE")
	 */
	protected $layout;
	
	public function __toString()
	{
		return $this->val;
	}


}
