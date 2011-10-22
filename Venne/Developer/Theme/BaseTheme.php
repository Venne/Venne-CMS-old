<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Theme;

/**
 * @author Josef Kříž
 */
abstract class BaseTheme implements ITheme {

	protected $context;
	
	/**
	 * @param Kdyby\DI\Container $context
	 */
	public function __construct(\Nette\DI\Container $context)
	{
		$this->context = $context;
	}

	public function getName()
	{
		return "undefined name";
	}


	public function getDescription()
	{
		return "undefined description";
	}


	public function getVersion()
	{
		return "undefined version";
	}


	public function setMacros(\Nette\Latte\Parser $parser)
	{
		\Venne\Latte\Macros\JsMacro::install($parser);
		\Venne\Latte\Macros\CssMacro::install($parser);
	}


	public function setTemplate(\Nette\Templating\ITemplate $template)
	{
		$template->setTranslator($this->context->translator);
		$template->registerHelper("thumb", '\Venne\Templating\ThumbHelper::thumb');
	}

}

