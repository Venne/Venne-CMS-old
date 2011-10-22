<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\HookModule;

use Venne;

/**
 * @author Josef Kříž
 */
class Manager extends \Nette\Object {

	const EXTENSION_CONTENT = "content";

	/** @var array */
	protected $hooks = array(
		'content\\extension\\form' => array(),
		'content\\extension\\save' => array(),
		'content\\extension\\load' => array(),
		'content\\extension\\remove' => array(),
		'content\\extension\\render' => array(),
		'admin\\menu' => array(),
	);


	/**
	 * @param string $name
	 * @param callback $callback
	 */
	public function addHook($name, $callback)
	{
		$this->hooks[$name][] = $callback;
	}


	/**
	 * @param string $name 
	 */
	public function registerHook($name)
	{
		$this->hooks[$name] = array();
	}


	/**
	 * @param string $name 
	 * @param array $args
	 */
	public function callHook($name)
	{
		$args = func_get_args();
		unset($args[0]);
		foreach ($this->hooks[$name] as $callback) {
			call_user_func_array($callback, $args);
		}
	}
	
	public function addHookExtension($extensionType, $class)
	{
		if($extensionType == self::EXTENSION_CONTENT){
			$this->addHook("content\\extension\\form", \callback($class, "hookContentExtensionForm"));
			$this->addHook("content\\extension\\load", \callback($class, "hookContentExtensionLoad"));
			$this->addHook("content\\extension\\save", \callback($class, "hookContentExtensionSave"));
			$this->addHook("content\\extension\\remove", \callback($class, "hookContentExtensionRemove"));
			$this->addHook("content\\extension\\render", \callback($class, "hookContentExtensionRender"));
		}
	}

}
