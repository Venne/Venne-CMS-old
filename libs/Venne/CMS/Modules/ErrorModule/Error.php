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
 * @Entity
 * @Table(name="error")
 * 
 * @property $text
 * @property $website
 * @property $code
 */
class Error extends \Venne\Models\BaseEntity{

	/**
	 * @Column(type="integer") 
	 */
	protected $code;

	/**
	 * @Column(type="text")
	 */
	protected $text;
	
	/**
	 * @ManyToOne(targetEntity="website", inversedBy="id")
	 * @JoinColumn(name="website_id", referencedColumnName="id")
	 */
	protected $website;

}
