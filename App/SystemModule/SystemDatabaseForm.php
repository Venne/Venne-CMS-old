<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\SystemModule;

use Venne\ORM\Column;
use Nette\Utils\Html;
use Venne\Forms\Form;

/**
 * @author Josef Kříž
 */
class SystemDatabaseForm extends \Venne\Developer\Form\EditForm {

	protected $mode;
	
	protected $testConnection;
	protected $showTestConnection;
	protected $createStructure;

	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $mode = "common", $showTestConnection = true, $testConnection = true, $createStructure = true)
	{
		$this->mode = $mode;
		$this->testConnection = $testConnection;
		$this->showTestConnection = $showTestConnection;
		$this->createStructure = $createStructure;
		parent::__construct($parent, $name);
	}	
	
	public function startup()
	{
		parent::startup();

		$this->addGroup();
		$this->addHidden("section")->setDefaultValue($this->mode);
		if($this->mode != "common") $this->addCheckbox("use", "Use for this mode");
		if($this->showTestConnection) $this->addCheckbox("test", "Test connection");
		else $this->addHidden("test");
		if($this->testConnection) $this["test"]->setDefaultValue(true);
		$this->addSelect("driver", "Driver", array("pdo_mysql" => "pdo_mysql", "pdo_pgsql" => "pdo_pgsql"));
		$this->addText("host", "Host");
		$this->addText("user", "User name");
		$this->addPassword("password", "Password");
		$this->addText("dbname", "Database");
		
		if($this->mode != "common") {
			$this["host"]
				->addConditionOn($this["use"], self::EQUAL, 1)
				->addRule(self::FILLED, 'Enter host');
			$this["user"]
				->addConditionOn($this["use"], self::EQUAL, 1)
				->addRule(self::FILLED, 'Enter user name');
			$this["dbname"]
				->addConditionOn($this["use"], self::EQUAL, 1)
				->addRule(self::FILLED, 'Enter database name');
		}else{
			$this["host"]
				->addRule(self::FILLED, 'Enter host');
			$this["user"]
				->addRule(self::FILLED, 'Enter user name');
			$this["dbname"]
				->addRule(self::FILLED, 'Enter database name');
		}
	}


	public function load()
	{
		$model = $this->presenter->context->services->system;
		
		$config = $model->loadDatabase($this->mode);
				
		$this->setDefaults($config);
		
		if($this->mode != "common"){
			$config2 = $model->loadDatabase("common");
			$ok = true;
			foreach($config as $key=>$item){
				if($config[$key] != $config2[$key]){
					$ok = false;
					break;
				}
			}
			if(!$ok){
				$this["use"]->setDefaultValue(true);
			}
		}
	}

	protected function handleError()
	{
		//$this->flashMessage("Cannot connect to database", "error");
		//$this->redirect("this");
	}

	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$model = $this->presenter->context->services->system;

		/*
		 * Test
		 */
		if($values["test"]){
			set_error_handler(array($this, 'handleError'));
			try{
				$db = new \Nette\Database\Connection(substr($values["driver"], 4) . ':host=' . $values["host"] . ';dbname=' . $values["dbname"], $values["user"], $values["password"]);
			}catch(\PDOException $e){
				$this->getPresenter()->flashMessage("Cannot connect to database ".$e->getMessage(), "warning");
				return false;
			}
			restore_error_handler();
		}
		
		if ($values["section"] == "common" || $values["use"]) {
			$model->saveDatabase($values["driver"], $values["host"], $values["dbname"], $values["user"], $values["password"], $values["section"]);
		}else{
			$config = $model->loadDatabase("common");
			$model->saveDatabase($config ["driver"], $config ["host"], $config ["dbname"], $config ["user"], $config ["password"], $values["section"]);
		}
		
		$this->presenter->context->params["database"]["driver"] = $values["driver"];
		$this->presenter->context->params["database"]["host"] = $values["host"];
		$this->presenter->context->params["database"]["dbname"] = $values["dbname"];
		$this->presenter->context->params["database"]["user"] = $values["user"];
		$this->presenter->context->params["database"]["password"] = $values["password"];
		
		if($this->createStructure){
			$em = $this->presenter->context->doctrineContainer->createServiceEntityManager();
	
			$classes = array();
			$robotLoader = $this->presenter->context->robotLoader;
			$robotLoader->rebuild();
			foreach($robotLoader->getIndexedClasses() as $key=>$item){
				if($key == "Venne\Testing\TestCase"){
					continue; // because Class 'PHPUnit_Framework_TestCase' not found
				}
				$class = "\\{$key}";
				try{
					$classReflection = new \Nette\Reflection\ClassType($class);
					if($classReflection->isSubclassOf("\\Venne\\Developer\\Doctrine\\BaseEntity")){
						$classes[] = $em->getClassMetadata($class);
					}
				}catch(\ReflectionException $e){
				}
			}
			$classes[] = $em->getClassMetadata("\App\SecurityModule\UserEntity");
			$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
			$tool->createSchema($classes);
			
			$admin = new \App\SecurityModule\RoleEntity;
			$admin->name = "admin";
			
			$guest = new \App\SecurityModule\RoleEntity;
			$guest->name = "guest";
			
			$registred = new \App\SecurityModule\RoleEntity;
			$registred->name = "registred";
			$registred->parent = $guest;
			
			$em->persist($admin);
			$em->persist($guest);
			$em->persist($registred);
			$em->flush();
		}
	}

}
