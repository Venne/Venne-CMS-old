<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Modules;

use Venne;

/**
 * @author Josef Kříž
 */
class SitemapService extends \Venne\Developer\Service\BaseService implements Venne\CMS\Developer\IFrontModule {

	public function getRoute(\Nette\Application\Routers\RouteList $router, $values = array(), $prefix = "") {
		$router[] = new \Nette\Application\Routers\Route($prefix. "sitemap-<sitemap>.xml", $values + array(
				'sitemap' => NULL,
				'module' => 'Sitemap',
				'presenter' => 'Default',
				'action' => 'sitemap'
			)
		);
		
		$router[] = new \Nette\Application\Routers\Route($prefix. "robots.txt", $values + array(
				'module' => 'Sitemap',
				'presenter' => 'Default',
				'action' => 'robots'
			)
		);
	}
	
}

