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
class AliasService
extends BaseService
implements
	\Venne\CMS\Developer\IContentExtensionModule,
	 \Venne\CMS\Developer\ICallbackModule	
{

	/** @var string */
	protected $className = "alias";

	public function getContentExtension()
	{
		return new AliasContentExtension($this->container);
	}


	public function onRender()
	{
		
	}


	public function onStartup()
	{
		$url = str_replace($this->container->httpRequest->url->getBasePath(), "", $this->container->httpRequest->url->getPath());
		$moduleName = $this->container->application->presenter->getModuleName();
		$alias = $this->getRepository()->findOneBy(array("moduleName"=>$moduleName, "url"=>$url));
		if($alias){
			$arr = array(
				"module" => "Default",
				"presenter" => "Default",
				"action" => "Default"
			);
			foreach($alias->keys as $item){
				$arr[$item->key] = $item->val;
			}
			
			$this->container->application->presenter->redirect(":".$arr["module"].":".$arr["presenter"].":".$arr["action"], $arr);
		}
	}


}

