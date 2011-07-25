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
 * Description of ExampleMacro
 *
 * @author Josef Kříž
 */
class ExampleMacro implements \Venne\Latte\IBaseMacro {
	
	public static function filter($content)
	{
		return 	' echo "hello '.$content.'"; ';
	}

	/**
	 * Adds 
	 */
	public static function register()
	{
		\Venne\Latte\DefaultMacros::$defaultMacros['example'] = '<?php %ExampleMacro::filter%; ?>';
	}
	
}

