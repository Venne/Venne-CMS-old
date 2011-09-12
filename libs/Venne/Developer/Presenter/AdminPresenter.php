<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Presenter;

use Venne;

/**
 * @author Josef Kříž
 */
class AdminPresenter extends \Venne\Application\UI\Presenter {


	/** @persistent */
	public $webId;

	public function startup()
	{
		/*
		 * Login
		 */
		if (!$this->getUser()->isLoggedIn() && $this->getName() != "Default:Admin:Login") {
			$this->redirect(":Default:Admin:Login:");
		}

		parent::startup();
	}


	public function beforeRender()
	{
		parent::beforeRender();
		$this->template->webId = $this->webId;
		
		$this->template->adminMenu = new \Nette\ArrayList;
		
		$this->context->hookManager->callHook("admin\\menu", $this->template->adminMenu);
	}

}

