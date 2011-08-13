<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Developer;

use Venne;

/**
 * Description of Element
 *
 * @author Josef Kříž
 */
class Element extends Venne\Application\UI\Control implements IElement {
	
	/** @var integer */
	protected $key;
	
	/** @var string */
	protected $name;
	
	/** @var array of params */
	protected $params;
	
	
	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL, $element, $key = NULL)
	{
		parent::__construct($parent, $name);
		$this->name = $element;
		$this->key = $key;
		
		$this->template->setTranslator($this->getContext()->ITranslator);
		$this->findTemplateFile();
	}
	
	/**
	 * @return array of string 
	 */
	public function findTemplateFile()
	{
		foreach($this->formatTemplateFiles() as $item){
			if(file_exists($item)){
				$this->template->setFile($item);
				return;
			}
		}
	}


	/**
	 * Formats view template file names.
	 * @return array
	 */
	public function formatTemplateFiles()
	{
		$dir = dirname($this->getReflection()->getFileName());
		$list = array(
			$this->getContext()->params["appDir"] . "/skins/" . $this->getContext()->website->current->skin . "/".  ucfirst($this->name) . "Element/template.latte",
			$dir . "/template.latte"
		);
		return $list;
	}


	public function startup()
	{
		
	}
	
	public function setParams()
	{
		$this->params = func_get_args();
	}
	
	/**
	 * Gets the context.
	 * @return Nette\DI\IContainer
	 */
	public function getContext()
	{
		return $this->getPresenter()->getContext();
	}
	
	public function beforeRender()
	{
		
	}

	public function render()
	{
		$this->startup();
		$this->beforeRender();
		$this->template->lang = $this->presenter->getLanguage()->getCurrentLang($this->presenter->getContext()->httpRequest)->lang;
		$this->template->langName = $this->presenter->getLanguage()->getCurrentLang($this->presenter->getContext()->httpRequest)->name;
		$this->template->langAlias = $this->presenter->getLanguage()->getCurrentLang($this->presenter->getContext()->httpRequest)->alias;
		
		if(isset($this->presenter->langEdit)) $this->template->langEdit = $this->presenter->langEdit;
		if(isset($this->presenter->webId)) $this->template->webId = $this->presenter->webId;
		if(!file_exists($this->template->getFile())) throw new \Nette\FileNotFoundException("Template for element not found. Missing template '".$this->template->getFile()."'.");
		
		$this->template->venneModeAdmin = $this->getContext()->params['venneModeAdmin'];
		$this->template->venneModeFront = $this->getContext()->params['venneModeFront'];
		$this->template->venneModeInstallation = $this->getContext()->params['venneModeInstallation'];
		
		$this->template->render();
	}
	
	public function flashMessage($message, $type = 'info')
	{
		return $this->getPresenter()->flashMessage($message, $type);
	}
	
}
