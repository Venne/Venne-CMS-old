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

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 * @Entity(repositoryClass="Venne\CMS\Modules\NavigationRepository")
 * @Table(name="navigation")
 * 
 * @property /Venne/CMS/Models/NavigationKey $keys
 * @property /Venne/CMS/Models/Navigation $childrens
 * @property /Venne/CMS/Models/Navigation $parent
 * @property bool $active
 * @property /Venne/CMS/Models/Website $website
 * @property string $type
 * @property string $name
 * @property string $moduleName
 * @property integer $moduleItemId
 */
class Navigation extends \Venne\Models\BaseEntity {

	const TYPE_URL = "url";
	const TYPE_LINK = "link";
	const TYPE_DIR = "dir";
	
	public function __construct()
	{
		$this->active = true;
		$this->keys = new \Doctrine\Common\Collections\ArrayCollection();
		$this->childrens = new \Doctrine\Common\Collections\ArrayCollection(); 
	}

	/**
	 *  @Column(type="boolean")
	 */
	protected $active;
	/**
	 * @OneToMany(targetEntity="navigation", mappedBy="parent")
	 */
	protected $childrens;
	/**
	 * @ManyToOne(targetEntity="navigation", inversedBy="id")
	 * @JoinColumn(name="navigation_id", referencedColumnName="id")
	 * @OrderBy({"order" = "ASC"})
	 */
	protected $parent;
	/**
	 * @ManyToOne(targetEntity="website", inversedBy="id")
	 * @JoinColumn(name="website_id", referencedColumnName="id")
	 */
	protected $website;
	/** @Column(type="string", length=30) */
	protected $type;
	/** @Column(type="string", length=300) */
	protected $name;
	/** @Column(type="string", length=300) */
	protected $moduleName;
	/** @Column(type="integer") */
	protected $moduleItemId;
	/**
	 * @OneToMany(targetEntity="navigationKey", mappedBy="navigation", indexBy="key", cascade={"persist", "remove", "detach"})
	 */
	protected $keys;
	protected $_link = NULL;
	protected $_active = NULL;

	public function setKeys($val)
	{
		$this->keys[$val->key] = $val;
		$val->navigation = $this;
	}
	
	public function getLink($presenter)
	{
		if (!$this->_link) {
			if ($this->type == "url") {
				$this->_link = $presenter->template->basePath . "/" . $this->keys["url"];
			} elseif ($this->type == "dir") {
				$data = $this->childrens;
				$data = $data[0];
				$this->_link = $data->url;
			} elseif ($this->type == "link") {
				$keys = array("module" => "Default", "presenter" => "Default", "action" => "default");
				if (isset($this->keys["module"]))
					$keys["module"] = $this->keys["module"];
				if (isset($this->keys["presenter"]))
					$keys["presenter"] = $this->keys["presenter"];
				if (isset($this->keys["action"]))
					$keys["action"] = $this->keys["action"];

				$args = array();
				$params = $presenter->getPersistentParams();
				foreach ($params as $param) {
					if ($param == "lang" || $param == "langEdit" || $param == "webId")
						continue;
					$args[$param] = NULL;
				}
				
				foreach($this->keys as $key){
					if ($key->key == "module" || $key->key == "presenter" || $key->key == "action"){
						continue;
					}
					$args[$key->key] = $key->val;
				}

				$this->_link = $presenter->link(":{$keys["module"]}:{$keys["presenter"]}:{$keys["action"]}", $args);
			}
		}
		return $this->_link;
	}

}
