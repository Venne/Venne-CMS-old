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
 * 
 * @property-read Venne\Application\Container $context
 */
class Presenter extends \Nette\Application\UI\Presenter {


	/* robots */
	CONST ROBOTS_INDEX = 1;
	CONST ROBOTS_NOINDEX = 2;
	CONST ROBOTS_FOLLOW = 4;
	CONST ROBOTS_NOFOLLOW = 8;

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

	public function getTheme()
	{
		return $this->context->themes->{$this->context->params["website"]["theme"]};
	}


	/**
	 * @return Doctrine\ORM\EntityManager 
	 */
	public function getEntityManager()
	{
		return $this->getContext()->doctrineContainer->entityManager;
	}


	/**
	 * @param \Nette\Application\UI\PresenterComponentReflection $element 
	 */
	public function checkRequirements($element)
	{
		if (!file_exists($this->context->params["flagsDir"] . "/installed") && substr($this->name, 0, 13) != "Installation:") {
			$this->redirect(":Installation:Admin:Default:", array('backlink' => $this->getApplication()->storeRequest()));
		}
	
		parent::checkRequirements($element);
		
		if (!$this->isMethodAllowed("startup")) {
			throw new \Nette\Application\ForbiddenRequestException;
		}
		
		$method = $this->formatActionMethod(ucfirst($this->getAction()));
		if (!$this->isMethodAllowed($method)) {
			throw new \Nette\Application\ForbiddenRequestException;
		}

		$signal = $this->getSignal();
		if ($signal) {
			$method = $this->formatSignalMethod(ucfirst($signal[1]));
			if (!$this->isMethodAllowed($method)) {
				throw new \Nette\Application\ForbiddenRequestException;
			}
		}
	}


	public function isMethodAllowed($method)
	{
		if (!$this->getReflection()->hasMethod($method)) {
			return true;
		}

		$data = \App\SecurityModule\Authorizator::parseAnnotations(get_called_class(), $method);

		if($data[\App\SecurityModule\Authorizator::RESOURCE] === NULL){
			return true;
		}
		
		if (!$this->user->isAllowed($data[\App\SecurityModule\Authorizator::RESOURCE], $data[\App\SecurityModule\Authorizator::PRIVILEGE])) {
			return false;
		}

		return true;
	}


	public function startup()
	{
		parent::startup();

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

		$this->getTheme()->setMacros($this->context->latteEngine->parser);
		//$template->registerFilter(new Venne\Latte\Engine($this->getContext()));
		
		$this->template->venneModeAdmin = $this->getContext()->params['venneModeAdmin'];
		$this->template->venneModeFront = $this->getContext()->params['venneModeFront'];
		$this->template->venneModeInstallation = $this->getContext()->params['venneModeInstallation'];
		$this->template->venneVersionId = VENNE_VERSION_ID;
		$this->template->venneVersionState = VENNE_VERSION_STATE;

		$this->onRender();

		\Venne\Panels\Stopwatch::start();
	}
	
	/**
	 * If Debugger is enabled, print template variables to debug bar
	 */
	protected function afterRender()
	{
		parent::afterRender();

		if (\Nette\Diagnostics\Debugger::isEnabled()) { // todo: as panel
			\Nette\Diagnostics\Debugger::barDump($this->template->getParams(), 'Template variables');
			$this->context->translatorPanel;
		}
	}


	public function shutdown($response)
	{
		parent::shutdown($response);
		\Venne\Panels\Stopwatch::stop("render template");
	}


	protected function createTemplate($class = NULL)
	{
		$template = $this->getContext()->templateContainer->createTemplate($this, $class);
		$this->getTheme()->setTemplate($template);
		return $template;
	}


	/**
	 * Descendant can override this method to customize template compile-time filters.
	 * @param  Nette\Templating\Template
	 * @return void
	 */
//	public function templatePrepareFilters($template)
//	{
//		// default filters
//		$template->registerFilter(new Venne\Latte\Engine($this->getContext()));
//	}


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
				$c_name = "\Venne\Elements\\" . \ucfirst($nameArr[1]) . "Element";
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
		$skinName = $this->getContext()->services->website->current->skin;
		if($this->getContext()->params["venneModeFront"]){
			$layout = $this->context->services->layout->detectLayout();
		}else{
			$layout = "layout";
		}
		$list = array(
			$this->getContext()->params["wwwDir"] . "/themes/$skinName/layouts/@$layout.latte"
		);
		return $list;
	}


	/**
	 * Formats view template file names.
	 * @return array
	 */
	public function formatTemplateFiles()
	{
		$skinName = $this->getContext()->services->website->current->skin;
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$dir = dirname(dirname($this->getReflection()->getFileName()));
		
		$path = str_replace(":", "Module/", substr($name, 0, strrpos($name, ":")))."Module";
		$subPath = substr($name, strrpos($name, ":") !== FALSE ? strrpos($name, ":") + 1 : 0);
		if ($path) {
			$path .= "/";
		}

		return array(
			$this->getContext()->params["wwwDir"] . "/themes/$skinName/templates/$path$presenter/$this->view.latte",
			$this->getContext()->params["wwwDir"] . "/themes/$skinName/templates/$path$presenter.$this->view.latte",
			"$dir/templates/$presenter/$this->view.latte",
			"$dir/templates/$presenter.$this->view.latte",
		);
	}


	/**
	 * @return string
	 */
	public function getModuleName()
	{
		return $this->moduleName;
	}


	/**
	 * @param type $destination 
	 */
	public function isAllowed($destination)
	{
		return true;
		if ($this->getContext()->params['venneModeInstallation'])
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
		$basePath = $this->getContext()->httpRequest->getUrl()->getBasePath();

		if ($url2 == $basePath && $link[0] == $basePath) {
			return true;
		}

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
		$this->getContext()->services->navigation->addPath($name, $url);
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

