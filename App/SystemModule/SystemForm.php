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
class SystemForm extends \Venne\Forms\EditForm {


	public function startup()
	{
		parent::startup();

		$this->addGroup();
		$this->addSelect("mode", "Mode", array(
				"production" => "production",
				"development" => "development",
				"console" => "console",
				"detect" => "detect"
			));
		$this->addTag("developerIp", "Developer IP");
	}


	public function load()
	{
		$model = $this->presenter->context->services->system;
	
		$this->setValues($model->loadGlobal());
	}

	public function save()
	{
		parent::save();
		$values = $this->getValues();
		$model = $this->presenter->context->services->system;

		$model->saveGlobal($values["mode"], $values["developerIp"]);
	}

}
