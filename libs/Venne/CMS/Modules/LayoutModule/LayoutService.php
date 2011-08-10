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
class LayoutService extends BaseService implements
\Venne\CMS\Developer\IModelModule, Venne\CMS\Developer\IAdminModule {


	/** @var string */
	protected $className = "layout";

	/**
	 * @return NavigationModel 
	 */
	public function createServiceModel()
	{
		return new LayoutModel($this->container, $this);
	}
	
	public function getAdminMenu()
	{
		$nav = new Navigation;
		$nav->name = "Layout";
		$nav->type = "link";
		
		$nav->keys["module"] = $key = new NavigationKey();
		$key->key = "module";
		$key->val = "Layout";
		
		return $nav;
	}

}

