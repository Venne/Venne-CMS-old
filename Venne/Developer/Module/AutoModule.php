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


	public function setPermissions(\Venne\Application\Container $container, \App\SecurityModule\Authorizator $permissions)
	{
		parent::setPermissions($container, $permissions);

		$cache = new \Nette\Caching\Cache($container->cacheStorage, "Venne.AutoModule");
		$resources = $cache->load($this->getName());
		if ($resources === NULL) {
			$resources = array();

			$paths = array(
				$container->params["appDir"] . "/" . ucfirst($this->getName()) . "Module",
				$container->params["libsDir"] . "/App/" . ucfirst($this->getName()) . "Module"
			);


			foreach ($paths as $path) {
				if (file_exists($path)) {
					foreach (array("" => $path . "/presenters", "\\AdminModule" => $path . "/AdminModule/presenters") as $key => $pathP) {
						if (file_exists($pathP)) {
							foreach (\Nette\Utils\Finder::findFiles("*Presenter.php")->in($pathP) as $file) {

								$className = "\\App\\" . ucfirst($this->getName()) . "Module{$key}\\" . substr($file->getBaseName(), 0, -4);

								$ref = new \Nette\Reflection\ClassType($className);
								if ($ref->hasAnnotation("resource")) {
									$resource = $ref->getAnnotation("resource");
									$j = explode("\\", $resource);
									unset($j[count($j) - 1]);
									$j = join("\\", $j);

									$resources[] = array("resource" => $resource, "privilege" => NULL, "parent" => $j);
								}

								foreach ($ref->getMethods() as $method) {
									if ($method->hasAnnotation("privilege")) {
										$resources[] = array("resource" => $resource, "privilege" => $method->getAnnotation("privilege"), "parent" => NULL);
									}
								}
							}
						}
					}
				}
			}
			sort($resources);
			$cache->save($this->getName(), $resources);
		}

		foreach ($resources as $item) {
			if (!$permissions->hasResource($item["resource"])) {
				$permissions->addResource($item["resource"], $item["parent"] ? $item["parent"] : NULL);
			}
			$permissions->addPrivilege($item["resource"], $item["privilege"]);
		}
	}

}

