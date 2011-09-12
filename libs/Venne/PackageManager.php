<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS;

use Venne;

/**
 * Description of ModuleActivator
 *
 * @author Josef Kříž
 */
class PackageManager extends \Venne\Models\DatabaseModel {
	
	protected $config;
	
	public function __construct(\Nette\DI\IContainer $container, $config)
	{
		parent::__construct($container);
		$this->config = $config;
	}
	
	public function getRepos()
	{
		return $this->config->repos;
	}
	
}
