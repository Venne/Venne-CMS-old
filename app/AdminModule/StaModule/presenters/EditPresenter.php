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
class EditPresenter extends BasePresenter {

	/** @persistent int */
	public $id;
	
	public function createComponentForm($name)
	{
		$form = new \StaModule\ItemForm($this, $name, $this->context->services->{"sta" . ucfirst($this->type)}->getRepository()->find($this->getParam("id")));
		$form->setSuccessLink("this");
		$form->setFlashMessage("Položka byla aktualizována");
		return $form;
	}

}
