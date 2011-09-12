<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Model;

/**
 * @author Josef Kříž
 */
class DoctrineModel extends BaseModel {


	/**
	 * @return \Venne\CMS\Developer\Doctrine\BaseRepository
	 */
	public function getRepository()
	{
		return $this->service->getRepository();
	}


	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		return $this->service->getEntityManager();
	}

}

