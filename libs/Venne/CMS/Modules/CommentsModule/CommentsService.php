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
class CommentsService
extends BaseService
implements
	\Venne\CMS\Developer\IContentExtensionModule,
	\Venne\CMS\Developer\IRenderableContentExtensionModule
{

	/** @var string */
	protected $className = "comments";

	public function getContentExtension()
	{
		return new CommentsContentExtension($this->container);
	}


	public function getContentExtensionElementName()
	{
		return "comments";
	}


}

