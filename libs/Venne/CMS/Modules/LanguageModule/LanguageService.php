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
class LanguageService extends BaseService {
	
	protected $className = "language";
	
	protected $currentLang;
	
	protected $currentFrontLang;
	
	public function getCurrentLang(\Nette\Http\Request $httpRequest)
	{
		if(!$this->currentLang){
			$repo = $this->getRepository();
			$website = $this->getContainer()->website->getCurrentWebsite($httpRequest);
			
			if(defined("VENNE_MODE_INSTALLATION")){
				$this->currentLang = new Language;
				$this->currentLang->name = "en";
				$this->currentLang->alias = "en";
				$this->currentLang->name = "english";
			}else if($website->langType == \Venne\CMS\Modules\Website::LANG_PARSE_URL){
				$url = explode("/", str_replace(".", "/", str_replace("http://", "", $httpRequest->getUrl())));
				$langAlias = $url[$website->langValue];
				
				$this->currentLang = $repo->findOneBy(array("website"=>$website->id, "alias"=>$langAlias));
				if(!$this->currentLang){
					$this->currentLang = $repo->findOneBy(array("id"=>$website->langDefault));
				}
			}else if($website->langType == \Venne\CMS\Modules\Website::LANG_IN_GET){
				$this->currentLang = $repo->findOneBy(array("website"=>$website->id, "alias"=>$httpRequest->getQuery($website->langValue)));
				if(!$this->currentLang){
					$this->currentLang = $repo->findOneBy(array("id"=>$website->langDefault));
				}
			}else{
				$this->currentLang = NULL;
			}
			if(!$this->currentLang){
				throw new BadLanguageException("Language doesn't exist");
			}
		}
		return $this->currentLang;
	}
	
	public function getCurrentFrontLang(\Nette\Http\Request $httpRequest)
	{
		if(!$this->currentFrontLang){
			$repo = $this->getRepository();
			$website = $this->getContainer()->website->getCurrentFrontWebsite($httpRequest);
			
			$langId = $httpRequest->getQuery("langEdit");
			if(!$langId){
				$this->currentFrontLang = $repo->findOneBy(array("id"=>$website->langDefault));
			}else{
				$this->currentFrontLang = $repo->findOneBy(array("alias"=>$langId, "website"=>$website->id));
			}
			if(!$this->currentFrontLang){
				throw new BadLanguageException("Language doesn't exist");
			}
		}
		return $this->currentFrontLang;
	}


	public function getCurrentLanguages($httpRequest)
	{
		$website = $this->getContainer()->website->getCurrentWebsite($httpRequest);
		$repo = $this->getRepository();
		
		$res = $repo->findBy(array("website"=>$website->id));

		$arr = array();
		foreach ($res as $doc) {
			$arr[$doc->id] = $doc;
		}

		return $arr;
	}
	
	public function getCurrentFrontLanguages($httpRequest)
	{
		$website = $this->getContainer()->website->getCurrentFrontWebsite($httpRequest);
		$repo = $this->getRepository();
		
		$res = $repo->findBy(array("website"=>$website->id));

		$arr = array();
		foreach ($res as $doc) {
			$arr[$doc->id] = $doc;
		}

		return $arr;
	}
	
	/**
	 * @param string $alias 
	 */
	public function getLanguageIdByAlias($alias)
	{
		return $this->getRepository()->findOneBy(array("alias"=>$alias))->id;
	}
	
	/**
	 * @param string $alias 
	 */
	public function getLanguageAliasById($id)
	{
		return $this->getRepository()->findOneBy(array("id"=>$id))->alias;
	}
	
}

