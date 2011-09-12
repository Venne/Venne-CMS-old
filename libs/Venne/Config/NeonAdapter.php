<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Config;

/**
 * @author Josef Kříž
 */
class NeonAdapter implements \Nette\Config\IAdapter {


	/**
	 * Static class - cannot be instantiated.
	 */
	final public function __construct()
	{
		throw new \Nette\StaticClassException;
	}


	/**
	 * @param mixed
	 * @param string  file
	 * @param type $mainSection
	 * @param type $inheritedSection 
	 * @return void
	 */
	public static function save($config, $file, $mainSection = NULL, $inheritedSections = array())
	{
		if (!is_array($inheritedSections)) {
			$inheritedSections = array($inheritedSections);
		}

		foreach ($inheritedSections as $mode) {
			$config[$mode] = self::optimize($config[$mode], $config[$mainSection]);
			$config[$mode . " < " . $mainSection] = $config[$mode];
			unset($config[$mode]);
		}

		\Nette\Config\NeonAdapter::save($config, $file);
	}


	/**
	 * @param array $array1
	 * @param array $array2
	 * @return array 
	 */
	private static function optimize($array1, $array2)
	{
		foreach ($array1 as $key => $item) {
			if (is_array($item)) {
				$ret = self::optimize($array1[$key], $array2[$key]);
				if (count($ret) == 0) {
					unset($array1[$key]);
				} else {
					$array1[$key] = $ret;
				}
			} else {
				if ($array1[$key] == $array2[$key]) {
					unset($array1[$key]);
				}
			}
		}
		return $array1;
	}


	/**
	 * Reads configuration from NEON file.
	 * @param  string  file name
	 * @return array
	 * @throws Nette\InvalidStateException
	 */
	public static function load($file)
	{
		return \Nette\Config\NeonAdapter::load($file);
	}

}

