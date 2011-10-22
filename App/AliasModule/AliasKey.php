<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\Modules;

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 * @Entity
 * @Table(name="aliasKey")
 * 
 * @property string $val
 * @property string $key
 * @property /Venne/CMS/Models/Alias $alias
 */
class AliasKey extends \Venne\Developer\Doctrine\BaseEntity{
	
	/**
	 *  @Column(type="string")
	 */
	protected $val;
	
	/**
	 *  @Column(name="`key`", type="string")
	 */
	protected $key;
	
	/**
	 * @ManyToOne(targetEntity="alias", inversedBy="id")
	 * @JoinColumn(name="alias_id", referencedColumnName="id", onDelete="CASCADE", onUpdate="CASCADE")
	 */
	protected $alias;
	
	public function __toString()
	{
		return $this->val;
	}


}
