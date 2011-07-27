<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Developer\Presenter;

use Venne;

/**
 * @author Josef Kříž
 */
class AdminPresenter extends \Venne\Application\UI\Presenter {


	/** @persistent */
	public $langEdit;
	/** @persistent */
	public $webId;

	public function startup()
	{
		/*
		 * Language
		 */
		if (!$this->lang) {
			$this->lang = $this->getLanguage()->getCurrentLang($this->getHttpRequest())->id;
		}
		if (!$this->langEdit) {
			$this->langEdit = $this->getLanguage()->getCurrentFrontLang($this->getHttpRequest())->id;
		}

		/*
		 * Website
		 */
		if (!$this->webId)
			$this->webId = $this->getWebsite()->currentFront->id;

		/*
		 * Login
		 */
		if (!$this->getUser()->isLoggedIn() && $this->getName() != "Login:Default") {
			$this->redirect(":Login:Default:");
		}

		parent::startup();
	}


	public function beforeRender()
	{
		parent::beforeRender();
		$this->template->langEdit = $this->langEdit;
		$this->template->webId = $this->webId;
	}

}

