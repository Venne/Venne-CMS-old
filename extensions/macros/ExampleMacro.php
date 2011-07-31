<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Latte\Macros;

/**
 * Description of ExampleMacro
 *
 * @author Josef Kříž
 */
class ExampleMacro extends \Nette\Latte\Macros\MacroSet {


	public static function filter(\Nette\Latte\MacroNode $node, $writer)
	{
		return ('echo "' . $node->args . '"; ');
	}


	/**
	 * Adds 
	 */
	public static function install(\Nette\Latte\Parser $parser)
	{
		$me = new static($parser);
		$me->addMacro('example', array($me, "filter"));
	}

}

