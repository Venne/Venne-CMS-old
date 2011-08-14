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

	const MODE_NORMAL = 0;
	const MODE_MODULE = 1;
	const MODE_LAYOUT = 2;
	const MODE_ELEMENTS = 3;
	
	public $contentExtensionsKey;
	
	/** @persistent */
	public $mode = 0;

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
	
	public function isModeNormal()
	{
		return ($this->mode == self::MODE_NORMAL);
	}

	public function isModeLayout()
	{
		return ($this->mode == self::MODE_LAYOUT);
	}

	public function isModeModule()
	{
		return ($this->mode == self::MODE_MODULE);
	}

	public function isModeElements()
	{
		return ($this->mode == self::MODE_ELEMENTS);
	}

}

