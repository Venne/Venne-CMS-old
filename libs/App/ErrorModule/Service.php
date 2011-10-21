<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace App\ErrorModule;

/**
 * @author Josef Kříž
 */
class Service extends \Venne\Developer\Service\DoctrineService implements \Venne\CMS\Developer\IErrorModule {


	protected $className = "error";
	public $entityNamespace = "\\App\\ErrorModule\\";

	/** @var \Venne\Application\Container */
	protected $context;


	public function __construct($context, $moduleName, \Doctrine\ORM\EntityManager $entityManager)
	{
		$this->context = $context;
		parent::__construct($moduleName, $entityManager);
	}


	/**
	 * @return ErrorModel 
	 */
	public function createServiceModel()
	{
		return new ErrorModel($this);
	}


	public function hookAdminMenu($menu)
	{
		$nav = new \App\NavigationModule\NavigationEntity("Error module");
		$nav->addKey("module", "Error:Admin");
		$menu[] = $nav;
	}


	public function saveError($code, $text)
	{
		$item = $this->getError($code);
		if (!$item) {
			$item = new ErrorEntity;
			$item->code = $code;
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
		return $this->getRepository()->findOneBy(array("code" => $code));
	}


	/**
	 * @param \Nette\Forms\IControl $control
	 * @return bool 
	 */
	public function isCodeAvailable(\Nette\Forms\IControl $control)
	{
		$code = $control->getValue();
		$website = $this->context->services->website->currentFront;

		$res = $this->getRepository()->findOneBy(array("code" => $code, "website" => $website->id));
		if (!$res) {
			return true;
		}
		return false;
	}


	public function getErrors()
	{
		$website = $this->context->services->website->currentFront;

		return $this->getRepository()->findAll();
	}

}