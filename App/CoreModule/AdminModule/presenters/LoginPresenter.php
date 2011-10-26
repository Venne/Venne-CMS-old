<?php

/**
 * My Application
 *
 * @copyright  Copyright (c) 2010 John Doe
 * @package    MyApplication
 */

namespace App\CoreModule\AdminModule;

use Nette\Application\UI,
	Nette\Security;

/**
 * Sign in/out presenters.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class LoginPresenter extends \Venne\Developer\Presenter\AdminPresenter {


	/** @persistent */
	public $backlink;


	/**
	 * Sign in form component factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm($name)
	{
		$form = new \App\SecurityModule\LoginForm($this, $name);
		$form->setSubmitLabel("Login");
		return $form;
	}


	public function beforeRender()
	{
		parent::beforeRender();
		$this->template->hideMenuItems = true;

		$this->setTitle("Venne:CMS | Login");
		$this->setKeywords("login");
		$this->setDescription("Login");
		$this->setRobots(self::ROBOTS_NOINDEX | self::ROBOTS_NOFOLLOW);
	}

}
