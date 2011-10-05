<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\NavigationModule;

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 * @Entity(repositoryClass="\Venne\Developer\Doctrine\BaseRepository")
 * @Table(name="navigation")
 * 
 * @property /Venne/CMS/Models/NavigationKeyEntity $keys
 * @property /Venne/CMS/Models/NavigationEntity $childrens
 * @property /Venne/CMS/Models/NavigationEntity $parent
 * @property bool $active
 * @property /Venne/CMS/Models/Website $website
 * @property string $type
 * @property string $name
 * @property string $moduleName
 * @property integer $moduleItemId
 */
class NavigationEntity extends \Venne\Developer\Doctrine\BaseEntity {


	const TYPE_URL = "url";
	const TYPE_LINK = "link";
	const TYPE_DIR = "dir";


	public function __construct($name = NULL)
	{
		if ($name) {
			$this->name = $name;
		}
		$this->active = true;
		$this->type = self::TYPE_LINK;
		$this->keys = new \Doctrine\Common\Collections\ArrayCollection();
		$this->childrens = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @Column(type="integer", name="`order`")
	 */
	protected $order;

	/**
	 *  @Column(type="boolean")
	 */
	protected $active;

	/**
	 * @OneToMany(targetEntity="navigationEntity", mappedBy="parent")
	 * @OrderBy({"order" = "ASC"})
	 */
	protected $childrens;

	/**
	 * @ManyToOne(targetEntity="navigationEntity", inversedBy="id")
	 * @JoinColumn(name="navigation_id", referencedColumnName="id", onDelete="CASCADE", onUpdate="CASCADE")
	 * @OrderBy({"order" = "ASC"})
	 */
	protected $parent;

	/** @Column(type="string", length=30) */
	protected $type;

	/** @Column(type="string") */
	protected $name;

	/** @Column(type="string", nullable=true) */
	protected $moduleName;

	/** @Column(type="string", nullable=true) */
	protected $moduleItemId;

	/**
	 * @OneToMany(targetEntity="navigationKeyEntity", mappedBy="navigation", indexBy="key", cascade={"persist", "remove", "detach"})
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
				$data = $this->childrens[0];
				$this->_link = $data->getLink($presenter);
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

				foreach ($this->keys as $key) {
					if ($key->key == "module" || $key->key == "presenter" || $key->key == "action") {
						continue;
					}
					$args[$key->key] = $key->val;
				}

				$this->_link = $presenter->link(":{$keys["module"]}:{$keys["presenter"]}:{$keys["action"]}", $args);
			}
		}
		return $this->_link;
	}


	public function addKey($key, $val)
	{
		$this->keys[$key] = $entity = new \Venne\NavigationModule\NavigationKeyEntity();
		$entity->navigation = $this;
		$entity->key = $key;
		$entity->val = $val;
	}

}
