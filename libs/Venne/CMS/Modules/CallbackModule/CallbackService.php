<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Modules;

use Venne;

/**
 * @author Josef Kříž
 */
class CallbackService extends BaseService implements \Venne\CMS\Developer\IStartupModule {


	const REMOVE_ITEM = 1;

	protected $aliases = array(
		1 => "onRemoveItem",
	);
	
	public function startup()
	{
		/*
		 * Active callbacks
		 */
		foreach ($this->container->moduleManager->getCallbackSenderModules() as $item) {
			$callbacks = $this->container->{$item}->getCallbacks();
			foreach ($callbacks as $key => $callback) {
				$this->container->{$item}->{$key}[] = callback($this->container->callback, "callbackListener" . ucfirst($callback));
			}
		}
	}


	public function callbackListener($name, $args)
	{
		if(isset($this->aliases[$name])){
			$name = $this->aliases[$name];
		}
		
		foreach ($this->getContainer()->moduleManager->getCallbackModules() as $item) {
			if(method_exists($this->getContainer()->{$item}, "slot" . ucfirst($name))){
				call_user_func_array(array($this->getContainer()->{$item}, "slot" . ucfirst($name)), $args);
			}
		}
	}


	public function __call($method, $args)
	{
		$callbackName = lcfirst(substr($method, 16));
		$this->callbackListener($callbackName, $args);
	}

}

