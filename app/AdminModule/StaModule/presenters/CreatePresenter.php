<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace AdminModule\StaModule;

/**
 * @author Josef Kříž
 * @resource AdminModule\StaModule\Security
 */
class CreatePresenter extends BasePresenter {
	
	public function createComponentForm($name)
	{
		$form = new \StaModule\ItemForm($this, $name);
		$form->setSuccessLink("this");
		$form->setFlashMessage("Položka byla uložena");
		return $form;
	}

}
