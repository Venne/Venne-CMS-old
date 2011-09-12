<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\Templating;

use Venne;

/**
 * Description of ThumbHelper
 *
 * @author Josef Kříž
 */
class ThumbHelper {
	
	
	/*	 * ************************* vytvareni miniatur ************************** */

	/** @var string relativni URI k adresari s miniaturami (zacina se v document_rootu) */
	public static $thumbDirUri = NULL;

	/**
	 * Vytvoreni miniatury obrazku a vraceni jeho URI
	 *
	 * @param  string relativni URI originalu (zacina se v document_rootu)
	 * @param  NULL|int sirka miniatury
	 * @param  NULL|int vyska miniatury
	 * @return string absolutni URI miniatury
	 */
	public static function thumb($origName, $width, $height = NULL, $flags = self::FIT, $crop = false, $tag = false)
	{
		if($tag){
			$tagWeb = \str_replace("-", "/", \Nette\Utils\Strings::webalize($tag));
			$thumbDirPath = WWW_DIR . '/' . trim(self::$thumbDirUri, '/\\').'/'.$tagWeb;
		}else{
			$thumbDirPath = WWW_DIR . '/' . trim(self::$thumbDirUri, '/\\');
		}
		$origPath = WWW_DIR . '/' . $origName;

		if (!\file_exists($thumbDirPath))
			\mkdir($thumbDirPath, 0777, true);
		if (($width === NULL && $height === NULL) || !is_file($origPath) || !is_dir($thumbDirPath) || !is_writable($thumbDirPath)){
			return $origName;
		}

		$thumbName = self::getThumbName($origName, $width, $height, filemtime($origPath), $flags, $crop);

		if($tag){
			$thumbUri = trim(self::$thumbDirUri, '/\\') . '/' . $tagWeb . '/' .$thumbName;
		}else{
			$thumbUri = trim(self::$thumbDirUri, '/\\') . '/' . $thumbName;
		}

		$thumbPath = $thumbDirPath . '/' . $thumbName;

		// miniatura jiz existuje
		if (is_file($thumbPath)) {
			return $thumbUri;
		}

		try {
			$image = \Nette\Image::fromFile($origPath);

			// zachovani pruhlednosti u PNG
			$image->alphaBlending(FALSE);
			$image->saveAlpha(TRUE);

			$origWidth = $image->getWidth();
			$origHeight = $image->getHeight();

			$image->resize($width, $height, $flags);
			$image->sharpen();

			$newWidth = $image->getWidth();
			$newHeight = $image->getHeight();

			if($crop){
				$image->crop(($newWidth-$width)/2, ($newHeight-$height)/2, $width, $height);
			}

			// doslo ke zmenseni -> ulozime miniaturu
			if ($newWidth !== $origWidth || $newHeight !== $origHeight) {

				$image->save($thumbPath);

				if (is_file($thumbPath)) {
					return $thumbUri;
				} else {
					return $origName;
				}
			} else {
				return $origName;
			}
		} catch (Exception $e) {
			return $origName;
		}
	}

	/**
	 * Vytvori jmeno generovane miniatury
	 *
	 * @param  string relativni cesta (document_root/$relPath)
	 * @param  int sirka
	 * @param  int vyska
	 * @param  int timestamp zmeny originalu
	 * @return string
	 */
	private static function getThumbName($relPath, $width, $height, $mtime, $flags, $crop)
	{
		$sep = '.';
		$tmp = explode($sep, $relPath);
		$ext = array_pop($tmp);

		// cesta k obrazku (ale bez pripony)
		$relPath = implode($sep, $tmp);

		// pripojime rozmery a mtime
		$relPath .= $width . 'x' . $height . '-' . $mtime . '.' . $flags. '.' . $crop;

		// zahashujeme a vratime priponu
		$relPath = md5($relPath) . $sep . $ext;

		return $relPath;
	}

	public static function deleteThumbsByTag($tag){
		\Benne\File::rmdir(WWW_DIR . '/' . trim(self::$thumbDirUri, '/\\').'/'. \str_replace("-", "/", \Nette\String::webalize($tag)));
	}
	
}

