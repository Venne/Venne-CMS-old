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
class ContentEntityForm extends EntityForm {

	/**
	 * Application form constructor.
	 */
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $key = NULL)
	{
		parent::__construct($parent, $name, $key);
		if (!$this["_submit"]->isSubmittedBy() && $this->key) {
			$this->presenter->context->hookManager->callHook("content\\extension\\load", $this, $this->getModuleName(), $this->getModuleItemId(), $this->getLinkParams());
		}
	}

	/**
	 * Application form constructor.
	 */
	public function startup()
	{
		parent::startup();
		$this->presenter->context->hookManager->callHook("content\\extension\\form", $this);
	}


	public function onSubmitForm()
	{
		if (!$this->isValid()) {
			return;
		}

		if ($this->save() === NULL) {
			if ($this->flash) {
				$this->getPresenter()->flashMessage($this->flash, $this->flashStatus);
			}
			if ($this->successLink) {
				$this->presenter->context->hookManager->callHook("content\\extension\\save", $this, $this->getModuleName(), $this->getModuleItemId(), $this->getLinkParams());
				$this->presenter->redirect($this->successLink, $this->successLinkParams);
			}
		}
	}

	protected function getLinkParams()
	{
		return array();
	}


	protected function getModuleName()
	{
		return "";
	}


	protected function getModuleItemId()
	{
		return NULL;
	}

}
