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
class LinkPresenter extends BasePresenter {

	/** @persistent int */
	public $id;
	
	public function actionTagInputSuggestNews($word_filter)
	{
		$form = $this->getComponent('form');
		$form['news']->renderResponse($this, $word_filter);
	}
	
	public function actionTagInputSuggestArticles($word_filter)
	{
		$form = $this->getComponent('form');
		$form['articles']->renderResponse($this, $word_filter);
	}
	
	public function actionTagInputSuggestFirms($word_filter)
	{
		$form = $this->getComponent('form');
		$form['firms']->renderResponse($this, $word_filter);
	}
	
	public function actionTagInputSuggestUsers($word_filter)
	{
		$form = $this->getComponent('form');
		$form['users']->renderResponse($this, $word_filter);
	}
	
	public function actionTagInputSuggestProducts($word_filter)
	{
		$form = $this->getComponent('form');
		$form['products']->renderResponse($this, $word_filter);
	}
	
	public function actionTagInputSuggestServices($word_filter)
	{
		$form = $this->getComponent('form');
		$form['services']->renderResponse($this, $word_filter);
	}
	
	public function createComponentForm($name)
	{
		$form = new \StaModule\LinkForm($this, $name, $this->context->services->{"sta" . ucfirst($this->type)}->getRepository()->find($this->getParam("id")));
		$form->setSuccessLink("this");
		$form->setFlashMessage("Vazby byly aktualizovány");
		return $form;
	}

}
