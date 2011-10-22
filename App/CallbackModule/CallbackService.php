<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\Modules;

use Venne;

/**
 * @author Josef Kříž
 */
class CallbackService extends \Venne\Developer\Service\BaseService implements \Venne\CMS\Developer\IStartupModule {

	const REMOVE_ITEM = 1;

	protected $aliases = array(
		1 => "onRemoveItem",
	);
	
	public function startup()
	{
		/*
		 * Active callbacks
		 */
		foreach ($this->container->cms->moduleManager->getCallbackSenderModules() as $item) {
			$callbacks = $this->container->cms->{$item}->getCallbacks();
			foreach ($callbacks as $key => $callback) {
				$this->container->cms->{$item}->{$key}[] = callback($this->container->callback, "callbackListener" . ucfirst($callback));
			}
		}
	}


	public function callbackListener($name, $args)
	{
		if(isset($this->aliases[$name])){
			$name = $this->aliases[$name];
		}
		
		foreach ($this->container->cms->moduleManager->getCallbackModules() as $item) {
			if(method_exists($this->container->cms->{$item}, "slot" . ucfirst($name))){
				call_user_func_array(array($this->container->cms->{$item}, "slot" . ucfirst($name)), $args);
			}
		}
	}


	public function __call($method, $args)
	{
		$callbackName = lcfirst(substr($method, 16));
		$this->callbackListener($callbackName, $args);
	}

}

