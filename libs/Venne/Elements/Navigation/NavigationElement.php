<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Elements;

use Venne;

/**
 * @author Josef Kříž
 */
class NavigationElement extends \Venne\Developer\Element\BaseElement {
	
	public function startup()
	{
		if ($this->key == "path") {
			$this->template->setFile(__DIR__ . "/path.latte");
		}
	}


	public function getItems()
	{
		if ($this->key == "main") {
			return $this->getContext()->services->navigation->getRootItems();
		} else if ($this->key == "path") {
			$this->template->setFile(__DIR__ . "/path.latte");
			return $this->getContext()->services->navigation->getPaths();
		}
	}

}
