<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Element;

use Venne;

/**
 * @author Josef Kříž
 */
interface IElement {
	
	public function setParams();

	public function startup();
	
}
