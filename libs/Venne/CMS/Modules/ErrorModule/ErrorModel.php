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
 * @property PagesService $parent
 */
class ErrorModel extends Venne\CMS\Developer\Model {


	public function saveError($code, $text)
	{
		$item = $this->getError($code);
		if (!$item) {
			$item = new Error;
			$item->code = $code;
			$item->website = $this->container->website->currentFront;
			$this->getEntityManager()->persist($item);
		}
		$item->text = $text;
		$this->getEntityManager()->flush();
	}


	public function removeError($id)
	{
		$item = $this->getRepository()->find($id);
		$this->getEntityManager()->remove($item);
		$this->getEntityManager()->flush();
	}


	public function getError($code)
	{
		$website = $this->container->website->currentFront;

		return $this->getRepository()->findOneBy(array("code" => $code, "website" => $website->id));
	}


	/**
	 * @param \Nette\Forms\IControl $control
	 * @return bool 
	 */
	public function isCodeAvailable(\Nette\Forms\IControl $control)
	{
		$code = $control->getValue();
		$website = $this->container->website->currentFront;

		$res = $this->getRepository()->findOneBy(array("code" => $code, "website" => $website->id));
		if (!$res) {
			return true;
		}
		return false;
	}


	public function getErrors()
	{
		$website = $this->container->website->currentFront;

		return $this->getRepository()->findBy(array("website" => $website->id));
	}

}

