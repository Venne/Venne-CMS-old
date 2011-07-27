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
 * @author Josef Kříž
 */
class Configurator {
	
	/** @var \Nette\DI\IContainer */
	protected $container;
	
	public function __construct(\Nette\DI\IContainer $container)
	{
		$this->container = $container;
	}
	
	/**
	 * @param string $name
	 * @param string $password 
	 */
	public function setAdminAccount($name, $password)
	{
		$config = \Nette\Config\NeonAdapter::load(WWW_DIR . "/../config.neon");
		$config["development"]["CMS"]["admin"]["name"] = $name;
		$config["development"]["CMS"]["admin"]["password"] = $password;
		$config = \Nette\Config\NeonAdapter::save($config, WWW_DIR . "/../config.neon");
	}
	
	/**
	 * @param string $driver
	 * @param string $host
	 * @param string $dbname
	 * @param string $user
	 * @param string $password
	 */
	public function setDatabase($driver, $host, $dbname, $user, $password)
	{
		$config = \Nette\Config\NeonAdapter::load(WWW_DIR . "/../config.neon");
		$config["development"]["database"]["driver"] = $driver;
		$config["development"]["database"]["host"] = $host;
		$config["development"]["database"]["dbname"] = $dbname;
		$config["development"]["database"]["user"] = $user;
		$config["development"]["database"]["password"] = $password;
		$config = \Nette\Config\NeonAdapter::save($config, WWW_DIR . "/../config.neon");
	}
	
	/**
	 * Create Database Structure
	 */
	public function createDatabaseStructure()
	{
		$this->container->database->loadFile(VENNE_DIR . "/CMS/sources/installation.sql");
	}


	/**
	 * @param string $name
	 * @param string $template
	 * @param string $regex
	 */
	public function createWebsite($name, $template, $regex)
	{
		$this->container->database->table("website")->insert(array("name"=>$name, "template"=>$template, "regex"=>$regex));
	}
	
	/**
	 * Set Installation Done
	 */
	public function setInstallationDone()
	{
		$handle = \fopen(TEMP_DIR . "/installed", 'w');
		\fwrite($handle, "");
		\fclose($handle);
	}
	
}
