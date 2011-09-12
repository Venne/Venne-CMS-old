<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CommentsModule;

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 * @Entity(repositoryClass="\Venne\Developer\Doctrine\BaseRepository")
 * @Table(name="comments")
 * 
 * @property string $moduleName
 * @property integer $moduleItemId
 */
class CommentsEntity extends \Venne\Developer\Doctrine\BaseEntity {

	public function __construct()
	{
		$this->moduleName = "";
		$this->moduleItemId = 0;
	}
	
	/** @Column(type="string", length=300) */
	protected $moduleName;
	/** @Column(type="integer") */
	protected $moduleItemId;

}
