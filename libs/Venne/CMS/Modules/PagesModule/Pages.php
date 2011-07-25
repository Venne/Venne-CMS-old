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
 * @Entity(repositoryClass="Venne\CMS\Modules\PagesRepository")
 * @Table(name="pages")
 * 
 * @property $mainPage
 * @property $title
 * @property $keywords
 * @property $description
 * @property $text
 * @property $created
 * @property $updated
 * @property $website
 * @property $urls
 */
class Pages extends \Venne\Models\BaseEntity{
	
	public function __construct()
	{
		if(!$this->created) $this->created = new \Nette\DateTime;
		if(!$this->updated) $this->updated = new \Nette\DateTime;
		$this->mainPage = false;
	}


	/**
	 * @Column(type="string")
	 * 
	 * @Form(group="Item")
	 */
	protected $title;
	
	/**
	 * @Column(type="string")
	 * 
	 * @Form
	 */
	protected $keywords;
	
	/**
	 * @Column(type="string")
	 * 
	 * @Form
	 */
	protected $description;
	
	/**
	 * @Column(type="text")
	 * 
	 * @Form
	 */
	protected $text;
	
	/**
	 * @Column(type="datetime")
	 */
	protected $created;
	
	/**
	 * @Column(type="datetime")
	 */
	protected $updated;
	
	/**
	 * @ManyToOne(targetEntity="website", inversedBy="id")
	 * @JoinColumn(name="website_id", referencedColumnName="id")
	 * 
	 * @Form(value="name")
	 */
	protected $website;
	
	/**
	 * @Column(type="string")
	 */
	protected $url;
	
	/**
	 * @Column(type="boolean")
	 */
	protected $mainPage;
	
}
