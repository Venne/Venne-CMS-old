<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace PagesModule;

/**
 * @author Josef Kříž
 * @Entity(repositoryClass="Venne\Developer\Doctrine\BaseRepository")
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
 * @property $url
 */
class PagesEntity extends \Venne\Developer\Doctrine\BaseEntity{
	
	public function __construct()
	{
		$this->created = new \Nette\DateTime;
		$this->updated = new \Nette\DateTime;
		$this->mainPage = false;
		$this->layout = "";
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
	 * @Column(type="string")
	 */
	protected $url;
	
	/**
	 * @Column(type="boolean")
	 */
	protected $mainPage;
	
	/**
	 * @Column(type="string")
	 */
	protected $layout;
	
}
