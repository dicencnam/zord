<?php
/**
* Lang
* @package Micro
* @subpackage Tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Lang {

	/**
	* Define client locale
	*/
	public static function defineLocale(){
		if(!isset($_SESSION['___LANG___'])){
			$acceptedLanguages = $_SERVER["HTTP_ACCEPT_LANGUAGE"];

			// regex inspired from @GabrielAnderson on
			// http://stackoverflow.com/questions/6038236/http-accept-language
			preg_match_all(
				'/([a-z]{1,8}(-[a-z]{1,8})*)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i',
				$acceptedLanguages,$lang_parse
			);
			$langs = $lang_parse[1];
			$ranks = $lang_parse[4];

			// create an associative array 'language' => 'preference'
			$lang2pref = array();
			for($i=0; $i<count($langs); $i++){
				$expLang = explode("-", $langs[$i]);
				if(count($expLang)>1)
					$lang2pref[$expLang[0].'-'.strtoupper($expLang[1])] = (float) (!empty($ranks[$i]) ? $ranks[$i] : 1);
			}

			// comparison function for uksort
			$cmpLangs = function ($a, $b) use ($lang2pref) {
				if ($lang2pref[$a] > $lang2pref[$b])
					return -1;
				elseif ($lang2pref[$a] < $lang2pref[$b])
					return 1;
				elseif (strlen($a) > strlen($b))
					return -1;
				elseif (strlen($a) < strlen($b))
					return 1;
				else
					return 0;
			};

			// sort the languages by prefered language and by the most specific region
			uksort($lang2pref, $cmpLangs);

			reset($lang2pref);
			$__l = key($lang2pref);
			include(CONFIG_FOLDER.'config_language.php');
			if (!in_array($__l, $zordLangs))
				$__l = $zordLangs[0];
			$_SESSION['___LANG___'] = $__l;
			//  level user admin (1) or simply user (0)
			$_SESSION['level'] = 0;
		}
		define('LANG',$_SESSION['___LANG___']);
	}

	/**
	* Locales
	*
	* @var Array
	*/
	protected static $_locales = array();

	/**
	* Load locales
	*
	* @param String $target Target of locale
	*/
	public static function load($target){
		$_l = array($target=>array());
		$file = LANG_FOLDER.LANG.DS.$target.'.json';
		if(!file_exists($file))
			$file = LANG_FOLDER.LANG_DEFAULT.DS.$target.'.json';

		if(file_exists($file))
			$_l[$target] = json_decode(file_get_contents($file));

		self::$_locales = array_merge(self::$_locales,$_l);
	}

	/**
	* Get target locale
	*
	* @param String $target Target of locale
	* @return Array
	*/
	public static function get($target,$key=null){
		if(!array_key_exists($target,self::$_locales))
			self::load($target);
		if($key!==null){
			if(array_key_exists($key,self::$_locales[$target]))
				return self::$_locales[$target]->$key;
			return '';
		}
		return self::$_locales[$target];
	}

	/**
	* Get all locales
	*
	* @return Array
	*/
	public static function getLocales(){
		return self::$_locales;
	}
}
?>
