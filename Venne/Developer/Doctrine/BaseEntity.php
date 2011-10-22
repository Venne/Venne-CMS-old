<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Doctrine;

/**
 * @author Josef Kříž
 */
class BaseEntity {


	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/*
	 * Magic
	 */


	public function __get($var)
	{
		if (is_callable(array($this, "get" . ucfirst($var)))) {
			return $this->{"get" . ucfirst($var)}();
		}
		if (isset($this->{$var}))
			return $this->{$var};
		return NULL;
	}


	public function __set($var, $value)
	{
		if (is_callable(array($this, "set" . ucfirst($var)))) {
			return $this->{"set" . ucfirst($var)}($value);
		}
		return $this->{$var} = $value;
	}

}

