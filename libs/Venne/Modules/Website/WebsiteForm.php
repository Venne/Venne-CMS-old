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
use Nette\Utils\Html;
use Nette\Forms\Form;

/**
 * @author Josef Kříž
 */
class WebsiteForm extends \Venne\Developer\Form\EditForm{
	
	public function startup()
	{
		parent::startup();
		$model = $this->getPresenter()->context->services->website;
		
		$this->addGroup("Routing");
		$this->addText("routePrefix", "Route prefix");
	}
	
	public function load()
	{
		$this->presenter->context->params["venne"]["website"];
	}


	public function save()
	{

	}

}
