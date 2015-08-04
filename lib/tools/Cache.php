<?php
/**
* Cache
* @package Micro
* @subpackage Tools
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Cache {

	/**
	* Set cache file
	*
	* @param String $label Label
	* @param String $data Data
	*/
	public static function set($label, $data){
		file_put_contents(self::_getFileName($label), $data);
	}

	/**
	* Get cache file
	*
	* @param String $label Label
	* @param Integer $cacheTime Time in cache
	* @return Mixed
	*/
	public static function get($label,$cacheTime=null){
		$file = self::_getFileName($label);
		if($cacheTime!==null)
			$cacheTime = CACHE_TIME;
		if(file_exists($file)){
				if(filemtime($file) + $cacheTime >= time())
					return file_get_contents($file);
				else
					unlink($file);
		}
		return false;
	}

	/**
	* Delete cache file
	*
	* @param String $label Label
	*/
	public static function del($label){
		$file = self::_getFileName($label);
		if(file_exists($file))
			unlink($file);
	}

	/**
	* Get file name
	*
	* @param String $label Label
	* @return String
	*/
	private static function _getFileName($label){
		return CACHE_FOLDER.md5($label).'.cache';
	}
}
?>
