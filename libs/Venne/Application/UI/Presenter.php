<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Application\UI;

use Venne;

/**
 * Description of Presenter
 *
 * @author Josef Kříž
 */
class Presenter extends \Nette\Application\UI\Presenter {


	/* robots */
	CONST ROBOTS_INDEX = 1;
	CONST ROBOTS_NOINDEX = 2;
	CONST ROBOTS_FOLLOW = 4;
	CONST ROBOTS_NOFOLLOW = 8;

	/** @persistent */
	public $lang;

	/* current module */
	protected $moduleName;

	/* vars for template */
	public $keywords;
	public $description;
	public $js = array();
	public $css = array();
	public $robots;
	public $author;
	public $title;

	/* callbacks */
	public $onStartup;
	public $onRender;


	/**
	 * @return Doctrine\ORM\EntityManager 
	 */
	public function getEntityManager()
	{
		return $this->getContext()->entityManager;
	}


	/**
	 * @return Venne\CMS\Modules\NavigationService
	 */
	public function getNavigation()
	{
		return $this->getContext()->navigation;
	}


	/**
	 * @return Venne\CMS\Modules\LanguageService
	 */
	public function getLanguage()
	{
		return $this->getContext()->language;
	}


	/**
	 * @return Venne\CMS\Website\WebsiteService
	 */
	public function getWebsite()
	{
		return $this->getContext()->website;
	}


	public function startup()
	{
		parent::startup();
		/*
		 * Add modules to DebugBar
		 */
		foreach ($this->getContext()->params["CMS"]["panels"] as $item) {
			$class = "\\Venne\\Panels\\" . ucfirst($item);
			\Nette\Diagnostics\Debugger::addPanel(new $class($this->getContext()));
		}
		$userPanel = new Venne\Panels\UserPanel($this->getContext());
		$userPanel->setNameColumn("name");
		\Nette\Diagnostics\Debugger::addPanel($userPanel);

		/*
		 * Security
		 */
		if (!$this->isAllowed("this")) {
			throw new \Exception("Low permission");
		}

		/*
		 * Add Callback modules
		 */
		if (!defined("VENNE_MODE_INSTALLATION")) {
			foreach ($this->getContext()->moduleManager->getCallbackModules() as $module) {
				$this->onStartup[] = callback($this->getContext()->{$module}, "onStartup");
				$this->onRender[] = callback($this->getContext()->{$module}, "onRender");
			}
		}



		/*
		 * Macros
		 */
		//\Venne\CMS\Macros\NConfirmMacro::register();

		/*
		 * Module
		 */
		$this->moduleName = lcfirst(substr($this->name, 0, strpos($this->name, ":")));


		/*
		 * Translator
		 */
		//$this->getContext()->translatorPanel;
		//if(file_exists(APP_DIR . "/".$this->module."Module/lang/".$this->getContext()->translator->getLang().".mo")){
//			$this->getContext()->translator->addDictionary($this->module."Module", APP_DIR . "/".$this->module."Module/");
//		}
//		if(file_exists(WWW_DIR . "/templates/".$this->getContext()->cms->website->getTemplateName()."/".$this->getContext()->translator->getLang().".mo")){
//			$this->getContext()->translator->addDictionary($this->getContext()->cms->website->getTemplateName().'Template', WWW_DIR . "/templates/".$this->getContext()->cms->website->getTemplateName());
//		}

		$this->onStartup();
	}


	public function beforeRender()
	{
		parent::beforeRender();
		$this->template->lang = $this->getLanguage()->getCurrentLang($this->getHttpRequest())->id;
		$this->template->langAlias = $this->getLanguage()->getCurrentLang($this->getHttpRequest())->alias;
		$this->template->langName = $this->getLanguage()->getCurrentLang($this->getHttpRequest())->name;

		$this->template->venneModeAdmin = defined('VENNE_MODE_ADMIN');
		$this->template->venneModeFront = defined('VENNE_MODE_FRONT');
		$this->template->venneModeInstallation = defined('VENNE_MODE_INSTALLATION');
		$this->template->venneVersionId = VENNE_VERSION_ID;
		$this->template->venneVersionState = VENNE_VERSION_STATE;

		$this->onRender();

		\Venne\Panels\Stopwatch::start();
	}


	public function shutdown($response)
	{
		parent::shutdown($response);
		\Venne\Panels\Stopwatch::stop("render template");
	}


	protected function createTemplate($class = NULL)
	{
		$template = parent::createTemplate($class);
		$template->setTranslator($this->getContext()->getService("ITranslator"));
		return $template;
	}


	/**
	 * Descendant can override this method to customize template compile-time filters.
	 * @param  Nette\Templating\Template
	 * @return void
	 */
	public function templatePrepareFilters($template)
	{
		// default filters
		$template->registerFilter(new Venne\Latte\Engine($this->getContext()));
	}


	/**
	 * Component factory. Delegates the creation of components to a createComponent<Name> method.
	 * @param  string      component name
	 * @return IComponent  the created component (optionally)
	 */
	public function createComponent($name)
	{
		if (substr($name, 0, 8) == "element_") {
			$nameArr = explode("_", $name, 3);
			$c_name = \ucfirst($nameArr[1]) . "Element";
			if (class_exists($c_name)) {
				$control = new $c_name($this, $name, $nameArr[1], isset($nameArr[2]) ? $nameArr[2] : NULL);
			} else {
				$c_name = "\Venne\CMS\Elements\\" . \ucfirst($nameArr[1]) . "Element";
				$control = new $c_name($this, $name, $nameArr[1], isset($nameArr[2]) ? $nameArr[2] : NULL);
			}
			return $control;
		} else {
			return parent::createComponent($name);
		}
	}


	/**
	 * Formats layout template file names.
	 * @return array
	 */
	public function formatLayoutTemplateFiles()
	{
		$template = $this->getWebsite()->current->template;
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$layout = $this->layout ? $this->layout : 'layout';
		$dir = dirname(dirname($this->getReflection()->getFileName()));
		$list = array(
			"$dir/templates/$presenter/@$layout.latte",
			"$dir/templates/$presenter.@$layout.latte",
			WWW_DIR . "/templates/$template/layouts/@$layout.latte",
		);
		do {
			$list[] = "$dir/templates/@$layout.latte";
			$dir = dirname($dir);
		} while ($dir && ($name = substr($name, 0, strrpos($name, ':'))));
		return $list;
	}


	/**
	 * @return string
	 */
	public function getModuleName()
	{
		return $this->moduleName;
	}


	/**
	 * @return mixed
	 */
	public function getModule()
	{
		return $this->getContext()->{$this->moduleName};
	}


	/**
	 * @return mixed
	 */
	public function getModel()
	{
		return $this->getModule()->model;
	}


	/**
	 * @param type $destination 
	 */
	public function isAllowed($destination)
	{
		if (defined("VENNE_MODE_INSTALLATION"))
			return true;
		if ($destination == "this") {
			$action = "action" . ucfirst($this->action);
			$class = $this;
		} else if (substr($destination, -1, 1) == "!") {
			$action = "handle" . ucfirst(substr($destination, 0, -1));
			$class = $this;
		} else {
			$destination = explode(":", $destination);
			if (count($destination) == 1) {
				$action = "action" . ucfirst($destination[count($destination) - 1]);
				$class = $this;
			} else {
				$action = "action" . ucfirst($destination[count($destination) - 1]);
				unset($destination[count($destination) - 1]);
				$class = "\\";
				foreach ($destination as $key => $item) {
					if ($key > 0) {
						$class .= "\\";
					}
					if ($key == count($destination) - 1) {
						$class .= $item . "Presenter";
					} else {
						$class .= $item . "Module";
					}
				}
			}
		}

		$annot = $this->getContext()->authorizator->getClassResource($class);
		if ($annot) {
			if (!$this->getUser()->isAllowed($annot)) {
				return false;
			}
		}

		$annot = $this->getContext()->authorizator->getMethodResource($class, $action);
		if ($annot) {
			if (!$this->getUser()->isAllowed($annot)) {
				return false;
			}
		}
		return true;
	}


	public function isCurrent($destination)
	{
		$reg = "/^" . str_replace("*", ".*", str_replace("#", "\/", $destination)) . "$/";
		return ((bool) preg_match($reg, $this->name . ":" . $this->view));
	}


	public function isCurrentUrl($url)
	{
		$url2 = $this->getContext()->httpRequest->getUrl()->getPath();
		$link = explode("?", $url);
		if (strpos($url2, $link[0]) === 0 && $link[0] != $this->getContext()->httpRequest->getUrl()->getBasePath()) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * @param string $text 
	 */
	public function setKeywords($text)
	{
		$this->keywords = $text;
	}


	/**
	 * @param string $text 
	 */
	public function setDescription($text)
	{
		$this->description = $text;
	}


	/**
	 * @param string $text 
	 */
	public function setTitle($text)
	{
		$this->title = $text;
	}


	/**
	 * @param string $text 
	 */
	public function setAuthor($text)
	{
		$this->author = $text;
	}


	/**
	 * @param string $content 
	 */
	public function addCss($content)
	{
		$this->css[$content] = $content;
	}


	/**
	 * @param string $content
	 */
	public function addJs($content)
	{
		$this->js[$content] = $content;
	}


	/**
	 * @param string $name
	 * @param string $url 
	 */
	public function addPath($name, $url)
	{
		$this->getContext()->navigation->model->addPath($name, $url);
	}


	/**
	 * @param int $content 
	 */
	public function setRobots($content)
	{
		$arr = array();

		if ($content & self::ROBOTS_INDEX)
			$arr[] = "index";
		if ($content & self::ROBOTS_NOINDEX)
			$arr[] = "noindex";
		if ($content & self::ROBOTS_FOLLOW)
			$arr[] = "follow";
		if ($content & self::ROBOTS_NOFOLLOW)
			$arr[] = "nofollow";

		$this->robots = join(", ", $arr);
	}

}

