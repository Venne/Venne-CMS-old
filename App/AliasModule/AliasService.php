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
class AliasService extends Venne\Developer\Service\DoctrineService implements
\Venne\CMS\Developer\IContentExtensionModule, \Venne\CMS\Developer\ICallbackModule, \Venne\CMS\Developer\IModelModule {


	/** @var string */
	protected $className = "alias";


	public function createServiceContentExtension()
	{
		return new AliasContentExtension($this->container);
	}


	public function slotOnPresenterStartup()
	{
		$url = str_replace($this->container->httpRequest->url->getBasePath(), "", $this->container->httpRequest->url->getPath());
		$moduleName = $this->container->application->presenter->getModuleName();
		if(isset($this->container->params["modules"][$moduleName."Module"]["routePrefix"])){
			$url = substr($url, strlen($this->container->params["modules"][$moduleName."Module"]["routePrefix"]));
		}
		$alias = $this->getRepository()->findOneBy(array("moduleName" => $moduleName, "url" => $url));
		if ($alias) {
			$arr = array(
				"module" => "Default",
				"presenter" => "Default",
				"action" => "Default"
			);
			foreach ($alias->keys as $item) {
				$arr[$item->key] = $item->val;
			}

			$this->container->application->presenter->redirect(":" . $arr["module"] . ":" . $arr["presenter"] . ":" . $arr["action"], $arr);
		}
	}


	/**
	 * @return AliasModel 
	 */
	public function createServiceModel()
	{
		return new AliasModel($this);
	}
	
	public function slotOnRemoveItem($moduleName, $moduleItemId)
	{
		$this->model->removeItemByModuleName($moduleName, $moduleItemId);
	}

}

