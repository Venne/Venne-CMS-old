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
class LayoutModel extends Venne\CMS\Developer\Model {

	public function getLayouts()
	{
		$website = $this->container->website->currentFront;
		
		return $this->getRepository()->findBy(array("website"=>$website->id));
	}
	
	public function removeLayout($id)
	{
		$this->getEntityManager()->remove($this->getRepository()->find($id));
		$this->getEntityManager()->flush();
	}
	
}

