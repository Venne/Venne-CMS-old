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
 * @Entity(repositoryClass="\Venne\Developer\Doctrine\BaseRepository")
 * @Table(name="alias")
 * 
 * @property string $url
 * @property string $moduleName
 * @property integer $moduleItemId
 */
class Alias extends \Venne\Developer\Doctrine\BaseEntity {


	/** @Column(type="string", length=300) */
	protected $url;
	/** @Column(type="string", length=300) */
	protected $moduleName;
	/** @Column(type="integer") */
	protected $moduleItemId;
	/**
	 * @OneToMany(targetEntity="aliasKey", mappedBy="alias", indexBy="key", cascade={"persist", "remove", "detach"})
	 */
	protected $keys;

	public function setKeys($val)
	{
		$this->keys[$val->key] = $val;
		$val->alias = $this;
	}

}
