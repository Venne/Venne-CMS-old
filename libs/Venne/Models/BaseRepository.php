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
class BaseRepository extends \Doctrine\ORM\EntityRepository {

	/**
	 * Fetches all records like $key => $value pairs
	 *
	 * @param string
	 * @param string
	 * @return array
	 */
	public function fetchPairs($key = NULL, $value = NULL)
	{
		$res = $this->createQueryBuilder('uni')->select("uni.$key, uni.$value")->getQuery()->getResult();

		$arr = array();
		foreach ($res as $row) {
			$arr[$row[$key]] = $row[$value];
		}

		return $arr;
	}
	
	/**
	 * Fetches all records and returns an associative array indexed by key
	 *
	 * @param string
	 * @return array
	 */
	public function fetchAssoc($key)
	{
		$res = $this->findAll();

		$arr = array();
		foreach ($res as $doc) {
			if (array_key_exists($doc->$key, $arr)) {
				throw new \Nette\InvalidStateException("Key value {$doc->{"get" . ucfirst($key)}} is duplicit in fetched associative array. Try to use different associative key");
			}
			$arr[$doc->{"get" . ucfirst($key)}()] = $doc;
		}

		return $arr;
	}
	
}

