<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace ErrorModule;

/**
 * @author Josef Kříž
 * @Entity
 * @Table(name="error")
 * 
 * @property $text
 * @property $website
 * @property $code
 */
class ErrorEntity extends \Venne\Developer\Doctrine\BaseEntity{

	/**
	 * @Column(type="integer") 
	 */
	protected $code;

	/**
	 * @Column(type="text")
	 */
	protected $text;

}
