<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */


/**
 * Description of Example
 *
 * @author Josef Kříž
 */
class ExampleElement extends \Venne\Developer\Element\BaseElement {
	
	public function render()
	{
		echo "name: " . $this->name . "<br>";
		if($this->key) echo "key: " . $this->key;
		dump($this->params);
	}
	
}
