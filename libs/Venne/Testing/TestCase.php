<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Testing;

use Venne;

/**
 * @author Josef Kříž
 * @author	Patrik Votoček
 */
class TestCase extends \PHPUnit_Framework_TestCase {


	/** @var \Nette\DI\Container */
	protected $context;

	/**
	 * Enable or disable the backup and restoration of the $GLOBALS array.
	 * Overwrite this attribute in a child class of TestCase.
	 * Setting this attribute in setUp() has no effect!
	 *
	 * @var bool
	 */
	protected $backupGlobals = FALSE;

	/**
	 * Enable or disable the backup and restoration of static attributes.
	 * Overwrite this attribute in a child class of TestCase.
	 * Setting this attribute in setUp() has no effect!
	 *
	 * @var bool
	 */
	protected $backupStaticAttributes = FALSE;


	public function runBare()
	{
		try {
			return parent::runBare();
		} catch (\Exception $e) {
			if (!$e instanceof \PHPUnit_Framework_AssertionFailedError) {
				\Nella\Diagnostics\ConsoleDebug::_exceptionHandler($e);
			}
			throw $e;
		}
	}


	protected function setup()
	{
		$this->context = clone \Nette\Environment::getContext();
	}

}