<?php

/**
 * My Application
 *
 * @copyright  Copyright (c) 2010 John Doe
 * @package    MyApplication
 */



/**
 * Homepage presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class HomepagePresenter extends Venne\CMS\Developer\Presenter\FrontPresenter
{
	
	public function renderDefault()
	{
		$this->getComponent("element_header");
		$this->template->anyVariable = 'any value';
	}

}
