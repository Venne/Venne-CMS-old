<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\CoreModule;

use Venne;

/**
 * @author Josef Kříž
 */
class ElementMacro extends \Nette\Latte\Macros\MacroSet {
	
	public static function dynamicVar($var)
	{
		$var = str_replace("'", '"', $var);
		if(substr($var, 0, 1) == '"' || substr($var, strlen($var)-1, 1) == '"') return $var;
		if(strpos($var, '"')) return $var;
		return '"' . $var . '"';
	}

	public static function filter2(\Nette\Latte\MacroNode $node, $writer)
	{
		$pair = $writer->fetchToken($node->args); // control[:method]
		if ($pair === NULL) {
			throw new ParseException("Missing control name in {control}", 0, $this->parser->line);
		}
		$pair = explode(':', $pair, 2);
		$name = str_replace('"', '', $writer->formatString($pair[0]));
		$key = isset($pair[1]) ? $pair[1] : '';
		$param = $writer->formatArray($content);
		if (strpos($content, '=>') === FALSE) {
			$param = substr($param, 6, -1); // removes array()
		}
		$key = self::dynamicVar($key);
		if($key != '""'){
			$key = substr($key, 0, 1) == '"' ? substr($key, 1) : '".'.$key;
			$name = '"element_".' . '"'.$name.'"' . '."_'. $key;
		}else{
			$name = '"element_".' . '"'.$name.'"' . '.""';
		}
		$writer->write('$_ctrl = $control->getPresenter()->getWidget(' . $name . '); '
				. 'if ($_ctrl instanceof Nette\Application\UI\IPartiallyRenderable) $_ctrl->validateControl(); '
				. "\$_ctrl->setParams($param); "
				. "\$_ctrl->render();");
	}
	
	/**
	 * {control name[:method] [params]}
	 */
	public function filter(\Nette\Latte\MacroNode $node, $writer)
	{
		$pair = $node->tokenizer->fetchWord();
		if ($pair === FALSE) {
			throw new ParseException("Missing control name in {control}");
		}
		$pair = explode(':', $pair, 2);
		$name = $writer->formatWord($pair[0]);
		$method = isset($pair[1]) ? '."_".' . $writer->formatWord($pair[1]) : "";
		$param = $writer->formatArray();
		if (strpos($node->args, '=>') === FALSE) {
			$param = substr($param, 6, -1); // removes array()
		}
		return ('$_ctrl = $control->getPresenter()->getWidget("element_".' . $name . $method .'); '
				. 'if ($_ctrl instanceof Nette\Application\UI\IPartiallyRenderable) $_ctrl->validateControl(); '
				. "\$_ctrl->setParams($param); "
				. "\$_ctrl->render();");
	}

	public static function install(\Nette\Latte\Parser $parser)
	{
		$me = new static($parser);
		$me->addMacro('element', array($me, "filter"));
	}
	
}

