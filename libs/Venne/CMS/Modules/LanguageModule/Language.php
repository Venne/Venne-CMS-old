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
 * @Entity(repositoryClass="Venne\CMS\Modules\LanguageRepository")
 * @Table(name="language")
 * 
 * @property string $lang
 * @property string $name
 * @property string $alias
 * @property \Venne\CMS\Modules\Website $website
 */
class Language extends \Venne\Models\BaseEntity{
	
	/**
	 *  @Column(type="string", length=3)
	 */
	protected $lang;
	
	/**
	 *  @Column(type="string", length=30)
	 */
	protected $name;
	
	/**
	 *  @Column(type="string", length=30)
	 */
	protected $alias;
	
	/**
	 * @ManyToOne(targetEntity="Website", inversedBy="id")
	 * @JoinColumn(name="website_id", referencedColumnName="id")
	 */
	protected $website;

}
