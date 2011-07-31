<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Latte;

use Venne;

/**
 * Description of Engine
 *
 * @author Josef Kříž
 */
class Engine extends \Nette\Latte\Engine {

	public function __construct(\Nette\DI\IContainer $container)
	{
		$this->parser = new \Nette\Latte\Parser();
		\Nette\Latte\Macros\CoreMacros::install($this->parser);
		$this->parser->addMacro('cache', new \Nette\Latte\Macros\CacheMacro($this->parser));
		\Nette\Latte\Macros\UIMacros::install($this->parser);
		\Nette\Latte\Macros\FormMacros::install($this->parser);
		
		/*
		 * Load macros
		 */
		foreach($container->params["venne"]["macros"] as $item){
			$class = "\Venne\Latte\Macros\\".ucfirst($item)."Macro";
			$class::install($this->parser);
		}
	}
	
}

