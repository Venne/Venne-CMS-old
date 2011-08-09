<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Modules;

use Venne;

/**
 * @author Josef Kříž
 */
class RepositoryService extends BaseService implements
Venne\CMS\Developer\IRouteModule, Venne\CMS\Developer\IModelModule, Venne\CMS\Developer\IAdminModule, \Venne\CMS\Developer\IInstallableModule {


	protected $className = "repository";
	
	public function getRoute(\Nette\Application\Routers\RouteList $router, $values = array(), $prefix = "")
	{
		$router[] = new \Nette\Application\Routers\Route($prefix . '<repository>/<package>', $values + array(
					'module' => 'Repository',
					'presenter' => 'Default',
					'action' => 'default',
					'url' => array(
						\Nette\Application\Routers\Route::PATTERN => '.*?',
						\Nette\Application\Routers\Route::FILTER_IN => NULL,
						\Nette\Application\Routers\Route::FILTER_OUT => NULL
					)
						)
		);
	}

	/**
	 * @return NavigationModel 
	 */
	public function createServiceModel()
	{
		return new RepositoryModel($this->container, $this);
	}
	
	public function getAdminMenu()
	{
		$nav = new Navigation;
		$nav->name = "Repositories";
		$nav->type = "link";
		
		$nav->keys["module"] = $key = new NavigationKey();
		$key->key = "module";
		$key->val = "Repository";
		
		return $nav;
	}
	
	public function installModule()
	{
		umask(0000);
		@mkdir(DATA_DIR . "/modules/repository/", 0777, true);
	}
	
	public function uninstallModule()
	{
		$dirContent = \Nette\Utils\Finder::find('*')->from(DATA_DIR . "/modules/repository")->childFirst();
		foreach ($dirContent as $file) {
			if ($file->isDir())
				@rmdir($file->getPathname());
			else
				@unlink($file->getPathname());
		}
		@rmdir(DATA_DIR . "/modules/repository");
	}

}

