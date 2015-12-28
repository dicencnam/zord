<?php
/**
* Dirs
* @package Micro
* @subpackage tools
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Dirs {

	/**
	* Delete a directory
	*
	* @param string $f File or directory
	*/
	public static function deleteDirectory($f){
		if(is_dir($f)){
			foreach(scandir($f) as $item ){
				if( !strcmp($item,'.') || !strcmp($item,'..') )
						continue;
				self::deleteDirectory($f.DS.$item );
			}
			rmdir($f);
		} else {
			if(is_file($f))
				unlink($f);
		}
	}

	/**
	* Deleting the contents of a directory
	*
	* @param String $directory Directory
	* @param Integer $mode
	*/
	public static function deleteContentsDirectory($directory,$mode=0777){
		self::deleteDirectory($directory);
		mkdir($directory,$mode);
	}

	/**
	* Copy a directory
	*
	* @param String $source File or directory source
	* @param String $destination File or directory destination
	*/
	static public function copyDirectory($source, $destination) {
		if(is_dir($source)) {
			$dir_handle = opendir($source);
			@mkdir($destination);
			while($file = readdir($dir_handle)){
				if($file!="." && $file!=".."){
					if(is_dir($source.DS.$file))
						self::copyDirectory($source.DS.$file, $dest.DS.$file);
					else
						copy($source.DS.$file, $destination.DS.$file);
				}
			}
			closedir($dir_handle);
		} else {
			copy($source, $dest);
		}
	}

	/**
	* Recursive glob
	*
	* @param String $pattern Pattern chek
	* @return Array
	*/
	public static function globRecursive($pattern) {
		$files = glob($pattern, 0);
		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
			$files = array_merge($files, self::globRecursive($dir.'/'.basename($pattern)));
		return $files;
	}

	/**
	* Clear tempory directory
	*/
	static public function clearTempDirectory($folder,$delay=1200) {
		$now = time();
		foreach (glob($folder."*") as $file){
			if(filemtime($file)+$delay<$now){//20mn
				if(is_dir($file))
				self::deleteDirectory($file);
				else
				unlink($file);
			}
		}
	}
}
?>
