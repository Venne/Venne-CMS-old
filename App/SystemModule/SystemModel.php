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
class SystemModel extends Venne\Developer\Model\BaseModel {


	/** @var array */
	protected $modes = array("development", "production", "console");
	/** @var \Nette\DI\IContainer */
	protected $container;


	public function __construct(\Nette\DI\IContainer $container)
	{
		$this->container = $container;
	}


	/**
	 * @return array
	 */
	public function loadConfig()
	{
		return \Nette\Config\NeonAdapter::load($this->container->params["wwwDir"] . "/../config.neon");
	}


	/**
	 * @param array $config 
	 */
	public function saveConfig($config)
	{
		\Venne\Config\NeonAdapter::save($config, $this->container->params["wwwDir"] . "/../config.neon", "common", $this->modes);
	}


	/**
	 * @param string $name
	 * @param string $password 
	 */
	public function setAdminAccount($name, $password, $section = "common")
	{
		$config = $this->loadConfig();
		$config[$section]["admin"]["name"] = $name;
		$config[$section]["admin"]["password"] = $password;
		$config = $this->saveConfig($config);
	}


	/**
	 * @param string $driver
	 * @param string $host
	 * @param string $dbname
	 * @param string $user
	 * @param string $password
	 * @param string $section
	 */
	public function saveDatabase($driver, $host, $dbname, $user, $password, $section = "common")
	{
		$config = $this->loadConfig();
		
		if($section == "common"){
			foreach($this->modes as $mode){
				if($mode == "common")					continue;
				if($config[$mode]["database"]["driver"] == $config["common"]["database"]["driver"]) $config[$mode]["database"]["driver"] = $driver;
				if($config[$mode]["database"]["host"] == $config["common"]["database"]["host"]) $config[$mode]["database"]["host"] = $host;
				if($config[$mode]["database"]["dbname"] == $config["common"]["database"]["dbname"]) $config[$mode]["database"]["dbname"] = $dbname;
				if($config[$mode]["database"]["user"] == $config["common"]["database"]["user"]) $config[$mode]["database"]["user"] = $user;
				if($config[$mode]["database"]["password"] == $config["common"]["database"]["password"]) $config[$mode]["database"]["password"] = $password;
			}
		}
		$config[$section]["database"]["driver"] = $driver;
		$config[$section]["database"]["host"] = $host;
		$config[$section]["database"]["dbname"] = $dbname;
		$config[$section]["database"]["user"] = $user;
		$config[$section]["database"]["password"] = $password;

		$this->saveConfig($config);
	}
	
	/**
	 * @param string $section
	 */
	public function loadDatabase($section = "common")
	{
		$config = $this->loadConfig();
		return $config[$section]["database"];
	}
	
	/**
	 * @return array
	 */
	public function loadGlobal()
	{
		$config = $this->loadConfig();
		return $config["global"];
	}
	
	/**
	 * @param string $mode
	 * @param array $developerIp 
	 */
	public function saveGlobal($mode, $developerIp = Null)
	{
		$config = $this->loadConfig();
		$config["global"]["mode"] = $mode;
		$config["global"]["developerIp"] = $developerIp;
		$this->saveConfig($config);
	}
	
	/**
	 * @return array
	 */
	public function loadAccount($section = "common")
	{
		$config = $this->loadConfig();
		return $config[$section]["admin"];
	}
	
	public function saveAccount($name, $password, $section = "common")
	{
		$config = $this->loadConfig();
		
		if($section == "common"){
			foreach($this->modes as $mode){
				if($mode == "common")					continue;
				if($config[$mode]["admin"]["name"] == $config["common"]["admin"]["name"]) $config[$mode]["admin"]["name"] = $name;
				if($config[$mode]["admin"]["password"] == $config["common"]["admin"]["password"]) $config[$mode]["admin"]["password"] = $password;
			}
		}
		$config[$section]["admin"]["name"] = $name;
		$config[$section]["admin"]["password"] = $password;

		$this->saveConfig($config);
	}


	/**
	 * Create Database Structure
	 * @param \Nette\Database\Connection $database
	 */
	public function createDatabaseStructure(\Nette\Database\Connection $database)
	{
		$database->loadFile($this->container->params["venneDir"] . "/CMS/sources/installation.sql");
	}


	/**
	 * Set Installation Done
	 */
	public function setInstallationDone()
	{
		$handle = \fopen($this->container->params["flagsDir"] . "/installed", 'w');
		\fwrite($handle, "");
		\fclose($handle);
	}

}