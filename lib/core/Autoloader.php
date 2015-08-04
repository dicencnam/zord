<?php
/**
* Filter autoloader
* @package Micro
* @subpackage Core
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class FilterAutoloader extends FilterIterator {

	/**
	* Extension PHP files
	*
	* @var String
	*/
	private $_ext = '.php';

	/**
	* Know if this is a good readable PHP file
	*
	* @return Boolean
	*/
	public function accept(){
		if (substr($this->current(), -1 * strlen($this->_ext)) === $this->_ext)
			return is_readable($this->current());

		return false;
	}
}

/**
* Autoloader
* @package Micro
* @subpackage Core
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Autoloader {

	/**
	* Singleton instance
	*
	* @var Object Autoloader
	*/
	private static $_instance = false;

	/**
	* Determines whether the list of class must be regenerated
	*
	* @var Boolean
	*/
	private $_regenerate = true;

	/**
	* Cache file
	*
	* @var String
	*/
	private $_cacheFile;

	/**
	* Class found
	*
	* @var String
	*/
	private $_classes = array();

	/**
	* Scan directory
	*
	* @var String
	*/
	private $_directory = '';

	/**
	* Constructor
	*/
	private function __construct(){}

	/**
	* Start autoloader
	*/
	public static function start(){
		if(self::$_instance === false){
			self::$_instance = new Autoloader();
			self::$_instance->_cacheFile = CACHE_FOLDER.'_autoloader.php';
			self::$_instance->_directory = LIB_FOLDER;
			self::$_instance->register();
		}
	}

	/**
	* Autoload register
	*/
	public function register(){
		spl_autoload_register(array($this, 'autoload'));
	}

	/**
	* Autoload
	*
	* @param String $className Class name
	* @return Mixed
	*/
	public function autoload($className){
		// class exist ?
		if ($this->_loadClass($className))
			return true;

		// Regenerate class list
		if ($this->_regenerate){
			$this->_regenerate = false;
			$this->_includesAll();
			$this->_saveInCache();
			return $this->autoload($className);
		}
		return false;
	}

	/**
	* Regenerate class list
	*/
	public function regenerate(){
		$this->_includesAll();
		$this->_saveInCache();
	}

	/**
	* Search all class
	*/
	private function _includesAll(){
		 $dir = new AppendIterator();
		 $dir->append(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->_directory)));

		 // Search php ile
		 $files = new FilterAutoloader($dir);

		 foreach($files as $fileName){
				$classes = $this->find((string) $fileName);
				foreach($classes as $className=>$fileName)
					 $this->_classes[$className] = $fileName;
		 }
	}

	/**
	*  Search class in the file
	*
	* @param String Path php file
	* @return Array
	*/
	public function find($file){
		$toReturn = array();
		$tokens = token_get_all(file_get_contents($file, false));
		$tokens = array_filter($tokens, 'is_array');

		$classHunt = false;
		foreach($tokens as $token){
			 if($token[0] === T_INTERFACE || $token[0] === T_CLASS){
					$classHunt = true;
					continue;
			 }

			 if ($classHunt && $token[0] === T_STRING){
					$toReturn[$token[1]] = $file;
					$classHunt = false;
			 }
		}
		return $toReturn;
	}

	/**
	* Save in cache class path
	*/
	private function _saveIncache(){
		$save = '<?php if ( ! defined(\'ROOT\')) exit(\'No direct script access allowed\'); $classes = '.var_export ($this->_classes, true).'; ?>';
		if (!file_exists(CACHE_FOLDER))
			mkdir(CACHE_FOLDER,0777);
		file_put_contents($this->_cacheFile, $save);
	}

	/**
	* Load class
	*
	* @param String Class name
	* @return Boolean
	*/
	private function _loadClass($className){
		if(count($this->_classes) === 0){
			 if (is_readable($this->_cacheFile)){
					require($this->_cacheFile);
					$this->_classes = $classes;
			 }
		}
		if (isset($this->_classes[$className])){
			 require_once($this->_classes[$className]);
			 return true;
		}
		return false;
	}

	/**
	* Test if a class exite
	*
	* @param String Class name
	* @return Boolean
	*/
	public static function classExit($className){
		return array_key_exists($className, self::$_instance->_classes);
	}

	/**
	* Return class path
	*
	* @param String Class name
	* @return String
	*/
	public static function classFile($className){
		return self::$_instance->_classes[$className];
	}
}
?>
