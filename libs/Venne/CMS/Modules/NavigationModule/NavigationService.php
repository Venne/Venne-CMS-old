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
class NavigationService extends BaseService implements
\Venne\CMS\Developer\IContentExtensionModule, \Venne\CMS\Developer\IModelModule {


	/** @var string */
	protected $className = "navigation";


	/**
	 * @return NavigationContentExtension 
	 */
	public function createServiceContentExtension()
	{
		return new NavigationContentExtension($this->container);
	}


	/**
	 * @return NavigationModel 
	 */
	public function createServiceModel()
	{
		return new NavigationModel($this->container, $this);
	}

}

