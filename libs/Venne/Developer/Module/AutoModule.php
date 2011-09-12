<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Module;

/**
 * @author Josef Kříž
 */
abstract class AutoModule extends BaseModule {

	public function setPermissions(\Venne\Application\Container $container, \Venne\Security\Authorizator $permissions)
	{
		parent::setPermissions($container, $permissions);
		
		$paths = array(
			"" => $container->params["appDir"] . "/" . ucfirst($this->getName()) . "Module/presenters",
			"\\AdminModule" => $container->params["appDir"] . "/AdminModule/" . ucfirst($this->getName()) . "Module/presenters",
		);

		$resources = array();
		
		foreach ($paths as $key => $path) {
			if (file_exists($path)) {
				foreach (\Nette\Utils\Finder::findFiles("*Presenter.php")->in($path) as $file) {
					$className = $key . "\\" . ucfirst($this->getName()) . "Module\\" . substr($file->getBaseName(), 0, -4);

					$ref = new \Nette\Reflection\ClassType($className);
					if ($ref->hasAnnotation("resource")) {
						$resource = $ref->getAnnotation("resource");
						$j = explode("\\", $resource);
						unset($j[count($j) - 1]);
						$j = join("\\", $j);

						$resources[] = array("resource"=>$resource, "privilege"=>NULL, "parent"=>$j);
					}

					foreach ($ref->getMethods() as $method) {
						if ($method->hasAnnotation("privilege")) {
							$resources[] = array("resource"=>$resource, "privilege"=>$method->getAnnotation("privilege"), "parent"=>NULL);
						}
					}
				}
			}
		}
		
		sort($resources);
				
		foreach($resources as $item){
			if(!$permissions->hasResource($item["resource"])){
				$permissions->addResource($item["resource"], $item["parent"] ? $item["parent"] : NULL);
			}
			$permissions->addPrivilege($item["resource"], $item["privilege"]);
		}
	}


}

