<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS;

use Venne;

/**
 * @author Josef Kříž
 * 
 * @property-read Venne\Modules\PagesService $pages
 * @property-read Venne\Modules\WebsiteService $website
 * @property-read Venne\Modules\AliasService $alias
 * @property-read Venne\Modules\CalbackService $callback
 * @property-read Venne\Modules\CommentsService $comments
 * @property-read Venne\Modules\ErrorService $error
 * @property-read Venne\Modules\LanguageService $language
 * @property-read Venne\Modules\LayoutService $layout
 * @property-read Venne\Modules\ModulesService $modules
 * @property-read Venne\Modules\NavigationService $navigation
 * @property-read Venne\Modules\RepositoryService $repository
 * @property-read Venne\Modules\SitemapService $sitemap
 * @property-read Venne\Modules\SystemService $system
 * @property-read Venne\Modules\UserService $users
 */
class Container extends \Nette\DI\Container {


	/** @var \Nette\DI\Container */
	protected $container;

	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(\Nette\DI\Container $container)
	{
		$this->container = $container;
		
		foreach ($this->moduleManager->getModules() as $item) {
			$class = "\Venne\Modules\\{$item}Service";
			$this->addService($item, function () use ($container, $item, $class) {return new $class($container, $item);});
		}
	}

	public function startup()
	{
		foreach ($this->moduleManager->getStartupModules() as $item) {
			$this->$item->startup();
		}
	}


	/**
	 * @return \Venne\CMS\ModuleManager
	 */
	public function createServiceModuleManager()
	{
		return new ModuleManager($this->container);
	}

}