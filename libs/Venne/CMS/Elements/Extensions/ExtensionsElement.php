<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Elements;

use Venne;

/**
 * @author Josef Kříž
 */
class ExtensionsElement extends \Venne\CMS\Developer\Element {


	public function startup()
	{
		if($this->getPresenter()->contentExtensionsKey !==NULL){
			$this->template->modules = $this->getContext()->moduleManager->getRenderableContentExtensionModules();
			$this->template->contentExtensionsKey = $this->getPresenter()->contentExtensionsKey;
			$this->template->moduleName = $this->getPresenter()->getModuleName();
		}
	}

}
