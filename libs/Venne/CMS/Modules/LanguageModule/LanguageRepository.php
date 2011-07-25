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
class LanguageRepository extends BaseRepository {
	
	protected $currentLang;
	
	protected $currentFrontLang;
	
	public function getCurrentLang(\Nette\Http\Request $httpRequest)
	{
		if(!$this->currentLang){
			$website = $this->getEntityManager()->getRepository("Venne\\CMS\\Modules\\Website")->getCurrentWebsite($httpRequest);
			
			if($website->langType == \Venne\CMS\Modules\Website::LANG_PARSE_URL){
				$url = explode("/", str_replace(".", "/", str_replace("http://", "", $httpRequest->getUrl())));
				$langAlias = $url[$website->langValue];
				
				$this->currentLang = $this->findOneBy(array("website"=>$website->id, "alias"=>$langAlias));
				if(!$this->currentLang){
					$this->currentLang = $this->findOneBy(array("id"=>$website->langDefault));
				}
			}else if($website->langType == \Venne\CMS\Modules\Website::LANG_IN_GET){
				$this->currentLang = $this->findOneBy(array("website"=>$website->id, "alias"=>$httpRequest->getQuery($website->langValue)));
				if(!$this->currentLang){
					$this->currentLang = $this->findOneBy(array("id"=>$website->langDefault));
				}
			}else{
				$this->currentLang = NULL;
			}
		}
		return $this->currentLang;
	}
	
	public function getCurrentFrontLang(\Nette\Http\Request $httpRequest)
	{
		if(!$this->currentFrontLang){
			$website = $this->getEntityManager()->getRepository("Venne\\CMS\\Modules\\Website")->getCurrentFrontWebsite($httpRequest);
			
			$langId = $httpRequest->getQuery("langEdit");
			if(!$langId){
				$this->currentFrontLang = $this->findOneBy(array("id"=>$website->langDefault));
			}else{
				$this->currentFrontLang = $this->findOneBy(array("id"=>$langId));
			}
		}
		return $this->currentFrontLang;
	}


	public function getCurrentLanguages($httpRequest)
	{
		$website = $this->getEntityManager()->getRepository("Venne\\CMS\\Modules\\Website")->getCurrentWebsite($httpRequest);
		
		$res = $this->findBy(array("website"=>$website->id));

		$arr = array();
		foreach ($res as $doc) {
			$arr[$doc->id] = $doc;
		}

		return $arr;
	}
	
	public function getCurrentFrontLanguages($httpRequest)
	{
		$website = $this->getEntityManager()->getRepository("Venne\\CMS\\Modules\\Website")->getCurrentFrontWebsite($httpRequest);
		
		$res = $this->findBy(array("website"=>$website->id));

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
		return $this->findOneBy(array("alias"=>$alias))->id;
	}
	
	/**
	 * @param string $alias 
	 */
	public function getLanguageAliasById($id)
	{
		return $this->findOneBy(array("id"=>$id))->alias;
	}
	
}

