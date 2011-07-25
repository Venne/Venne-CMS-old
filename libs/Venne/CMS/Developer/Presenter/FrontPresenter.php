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
class FrontPresenter extends \Venne\Application\UI\Presenter {

	public $contentExtensionsKey;

	public function startup()
	{
		parent::startup();

		/*
		 * Language
		 */
		if (!$this->lang) {
			$this->lang = $this->getLanguage()->getCurrentLang($this->getHttpRequest())->id;
		}
	}

}

