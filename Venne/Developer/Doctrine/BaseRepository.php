<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Developer\Doctrine;

use Venne;

/**
 * @author Josef Kříž
 * @author	Patrik Votoček
 */
class BaseRepository extends \Doctrine\ORM\EntityRepository {

	/**
	 * Does an entity with a key equal to value exist?
	 *
	 * @param string
	 * @param mixed
	 * @return bool
	 */
	public function doesExistByColumn($key, $value)
	{
		$res = $this->findOneBy(array($key => $value));
		return!empty($res);
	}


	/**
	 * Does an entity with key equal to value exist and is not same as given entity id?
	 *
	 * @param string
	 * @param string
	 * @param mixed
	 * @return bool
	 */
	public function isColumnUnique($id, $key, $value)
	{
		$res = $this->findOneBy(array($key => $value));
		return empty($res) ? : $res->id == $id;
	}


	/**
	 * Fetches all records that correspond to ids of a given array
	 *
	 * @param array
	 * @return array
	 */
	public function findByIds(array $ids)
	{
		$arr = array();
		$qb = $this->createQueryBuilder('uni');
		$qb->where($qb->expr()->in('uni.id', $ids));
		foreach ($qb->getQuery()->getResult() as $res) {
			$arr[$res->id] = $res;
		}

		return $arr;
	}


	/**
	 * Fetches all records like $key => $value pairs
	 *
	 * @param string
	 * @param string
	 * @return array
	 */
	public function fetchPairs($key = NULL, $value = NULL, $where = array())
	{
		$res = $this->createQueryBuilder('uni')->select("uni.$key, uni.$value");
		foreach ($where as $key2 => $item) {
			$res->where("uni.$key2 = :$key2")->setParameter($key2, $item);
		}
		$res = $res->getQuery()->getResult();

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
	public function fetchAssoc($key, $where = array())
	{
		$res = $this->createQueryBuilder('uni')->select("uni");
		foreach ($where as $key2 => $item) {
			$res->where("uni.$key2 = :$key2")->setParameter($key2, $item);
		}
		$res = $res->getQuery()->getResult();

		$arr = array();
		foreach ($res as $doc) {
			if (array_key_exists($doc->$key, $arr)) {
				throw new \Nette\InvalidStateException("Key value {$doc->{"get" . ucfirst($key)}} is duplicit in fetched associative array. Try to use different associative key");
			}
			$arr[$doc->$key] = $doc;
		}

		return $arr;
	}

}

