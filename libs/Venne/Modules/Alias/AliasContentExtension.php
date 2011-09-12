<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Modules;

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 */
class AliasContentExtension extends \Venne\Developer\ContentExtension\BaseContentExtension implements \Venne\Developer\ContentExtension\IContentExtension {


	public function saveForm(\Nette\Forms\Container $container, $moduleName, $moduleItemId, $linkParams)
	{
		$values = $container->getValues();
		$model = $this->container->cms->alias->model;
		
		$model->saveItems($moduleItemId, $moduleName, $values, $linkParams);
	}


	public function setForm(\Nette\Forms\Container $container)
	{
		$container->addTag("urls", "URL alias");
	}


	public function setValues(\Nette\Forms\Container $container, $moduleName, $moduleItemId, $linkParams)
	{
		$model = $this->container->cms->alias->model;
		
		$container["urls"]->setDefaultValue($model->getItems($moduleItemId, $moduleName));
	}

}
