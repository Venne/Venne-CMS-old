<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Elements;

use Venne;

/**
 * @author Josef Kříž
 */
class NavigationElement extends \Venne\Application\UI\Element {


	public function startup()
	{
		if ($this->key == "main") {
			$this->template->items = $this->getContext()->navigation->model->getRootItems();
		} else if ($this->key == "path") {
			$this->template->setFile(__DIR__ . "/path.latte");
			$this->template->items = $this->getContext()->navigation->model->getPaths();
		}
	}

}
