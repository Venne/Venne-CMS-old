<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Forms;

/**
 * @author     Josef Kříž
 */
class EditForm extends Form {


	/** @var string */
	protected $key;

	/** @var string */
	protected $successLink;

	/** @var string */
	protected $successLinkParams;

	/** @var string */
	protected $flash;

	/** @var string */
	protected $flashStatus;


	/**
	 * Application form constructor.
	 */
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $key = NULL)
	{
		$this->key = $key;
		parent::__construct($parent, $name);
		$this->startup();
		$this->setCurrentGroup();
		$this->flashStatus = $this->getPresenter()->getContext()->params['flashes']['success'];
		$this->addSubmit("_submit", "Submit")->onClick[] = callback($this, 'onSubmitForm');
		if (!$this["_submit"]->isSubmittedBy() && $this->key && !$this->isSubmitted()) {
			$this->load();
		}
	}


	public function onSubmitForm()
	{
		if (!$this->isValid()) {
			return;
		}

		if ($this->save() === NULL) {
			if ($this->flash)
				$this->getPresenter()->flashMessage($this->flash, $this->flashStatus);
			if ($this->successLink && !$this->presenter->isAjax()) {
				$this->presenter->redirect($this->successLink, $this->successLinkParams);
			}
		}
	}


	public function setSuccessLink($link, $params = NULL)
	{
		$this->successLink = $link;
		$this->successLinkParams = (array) $params;
	}


	public function setFlashMessage($value, $status = NULL)
	{
		if ($status) {
			$this->flashStatus = $status;
		}
		$this->flash = $value;
	}


	public function setKey($key)
	{
		$this->key = $key;
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


	public function save()
	{
		
	}


	public function startup()
	{
		
	}

}
