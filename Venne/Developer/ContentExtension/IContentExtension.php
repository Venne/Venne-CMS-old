<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\ContentExtension;

/**
 * @author Josef Kříž
 */
interface IContentExtension {


	public function hookContentExtensionSave(\Nette\Forms\Container $form, $moduleName, $moduleItemId, $linkParams);


	public function hookContentExtensionForm(\Nette\Forms\Container $form);


	public function hookContentExtensionLoad(\Nette\Forms\Container $form, $moduleName, $moduleItemId, $linkParams);


	public function hookContentExtensionRemove($moduleName, $moduleItemId);
	
	public function hookContentExtensionRender($presenter, $moduleName);
}

