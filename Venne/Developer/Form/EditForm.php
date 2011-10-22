<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Form;

/**
 * @author     Josef Kříž
 */
class EditForm extends BaseForm {


	/** @var string */
	protected $key;


	/**
	 * Application form constructor.
	 */
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $key = NULL)
	{
		$this->key = $key;
		parent::__construct($parent, $name);
		$this->addSubmit("_submit", "Submit")->onClick[] = callback($this, 'onSubmitForm');
		if (!$this["_submit"]->isSubmittedBy() && $this->key && !$this->isSubmitted()) {
			$this->load();
		}
	}
	
	public function getKey()
	{
		return $this->key;
	}


	public function setSubmitLabel($label)
	{
		$this["_submit"]->caption = $label;
	}


	public function load()
	{
		
	}

}
