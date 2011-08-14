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

use Venne\ORM\Column;

/**
 * @author Josef Kříž
 */
class LayoutContentExtension extends BaseService implements \Venne\CMS\Developer\IContentExtension {


	public function saveForm(\Nette\Forms\Container $container, $moduleName, $moduleItemId, $linkParams)
	{
		$values = $container->getValues();
		$model = $this->container->layout->model;
		
		$model->saveLayout($moduleItemId, $moduleName, $values["use"], $values["layout"], $linkParams);
	}


	public function setForm(\Nette\Forms\Container $container)
	{
		$container->addCheckbox("use", "Set layout")->setDefaultValue(false);
		$container->addSelect("layout", "Layout", $this->container->moduleManager->getLayouts());
	}


	public function setValues(\Nette\Forms\Container $container, $moduleName, $moduleItemId, $linkParams)
	{
		$model = $this->container->layout->model;
		
		$values = $model->loadLayout($moduleItemId, $moduleName);
		
		if($values){
			$container["use"]->setValue(true);
			$container["layout"]->setValue($values->layout);
		}
	}

}
