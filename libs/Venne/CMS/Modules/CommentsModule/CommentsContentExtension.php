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
class CommentsContentExtension extends BaseService implements \Venne\CMS\Developer\IContentExtension {


	public function saveForm(\Nette\Forms\Container $container, $moduleName, $moduleItemId, $linkParams)
	{
		$values = $container->getValues();
		$model = $this->container->comments->model;
		
		$model->saveSetting($moduleItemId, $moduleName, $values["use"]);
	}


	public function setForm(\Nette\Forms\Container $container)
	{
		$container->addCheckbox("use", "Allow comments")->setDefaultValue(false);
	}


	public function setValues(\Nette\Forms\Container $container, $moduleName, $moduleItemId, $linkParams)
	{
		$model = $this->container->comments->model;
		
		$container["use"]->setValue($model->getSetting($moduleItemId, $moduleName));
	}

}
