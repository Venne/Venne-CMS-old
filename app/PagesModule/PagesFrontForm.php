<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace PagesModule;

use Venne\ORM\Column;
use Nette\Utils\Html;
use Venne\Forms\Form;
/**
 * @author Josef Kříž
 */
class PagesFrontForm extends \Venne\Developer\Form\BaseForm {


	public function startup()
	{
		parent::startup();
		
		$this->addTextArea("text", "", Null, 20);
		$this->onSuccess[] = callback($this, 'onSubmitForm');
	}
	
	public function load()
	{
		$entity = $this->getPresenter()->getContext()->cms->pages->getRepository()->findOneByUrl($this->getPresenter()->getParam("url"));
		$this["text"]->setDefaultValue($entity->text);
	}

	public function save()
	{
		parent::save();
		$values = $this->getValues();
		
		$entity = $this->getPresenter()->getContext()->cms->pages->getRepository()->findOneByUrl($this->getPresenter()->getParam("url"));
		$entity->text = $values["text"];
		$this->getPresenter()->getContext()->doctrine->entityManager->flush();
		die("ok");
	
	}


	

}
