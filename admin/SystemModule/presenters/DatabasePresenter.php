<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace SystemModule;

/**
 * @author Josef Kříž
 * @allowed(administration-system)
 */
class DatabasePresenter extends BasePresenter
{
	
	public function startup()
	{
		parent::startup();
		$this->addPath("Database", $this->link(":System:Database:"));
	}
	
	public function createComponentFormEdit($name)
	{
		$form = new \Venne\CMS\Modules\SystemDatabaseForm($this, $name, $this->mode);
		$form->setSuccessLink("default");
		$form->setFlashMessage("Database settings has been updated");
		$form->setSubmitLabel("Update");
		return $form;
	}
	
	public function renderDefault()
	{

	}

}
