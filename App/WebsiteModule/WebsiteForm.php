<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\WebsiteModule;

use Venne\ORM\Column;
use Nette\Utils\Html;
use Nette\Forms\Form;

/**
 * @author Josef Kříž
 */
class WebsiteForm extends \Venne\Forms\EditForm{
	
	public function startup()
	{
		parent::startup();
		$skins = $this->presenter->context->services->modules->getThemes();
		
		$this->addGroup("Themes");
		$arr = array();
		foreach($skins as $skin){
			if($skin == "admin"){
				continue;
			}
			$arr[$skin] = $this->presenter->context->themes->{$skin}->getDescription();
		}
		
		$this->addRadioList("theme", "Website theme", $arr);
		$this["theme"]->setDefaultValue($this->presenter->context->params["website"]["theme"]);
	}
	
	public function save()
	{
		$this->presenter->context->services->website->setTheme($this["theme"]->getValue());
	}

}
