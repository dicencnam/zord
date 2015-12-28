<?php
/**
* Filter
* @package zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Filter {

	/**
	* JSON filter
	*
	* @param string $str
	* @return array
	*/
	public static function json($str) {
		static $patterns = null;
		static $replacements = null;
		static $translation = null;
		if ($translation === null) {
			$translation = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
			foreach ($translation as $k => $v) {
				$patterns[] = "/$v/";
				if($k=='"')
				$replacements[] = '"';
				else
				$replacements[] = '&#' . ord($k) . ';';
			}
		}
		return json_decode(preg_replace($patterns, $replacements, htmlentities($str, ENT_QUOTES, 'UTF-8')));
	}

	/**
	* Valid portal name
	*
	* @param string $str
	* @return boolean
	*/
	public static function validPortal($portal) {
		include(CONFIG_FOLDER.'config_portals.php');
		if(in_array($portal, $websites))
			return true;
		return false;
	}

	/**
	* Valid Book name
	*
	* @param string $str
	* @return boolean
	*/
	public static function validEAN13($ean){
		if(gettype($ean)=='integer')
			$ean = (string) $ean;

		if(gettype($ean)==='string' && ctype_digit($ean) && strlen($ean)==13)
			return true;
		return false;
	}

	/**
	* TEI source structure filter
	*
	* @param string $str
	* @return array
	*/
	public static function sourceStucture($str){
		return Tool::objectToArray(json_decode($str));
	}

	/**
	* TEI source IDs filter
	*
	* @param string $str
	* @return array
	*/
	public static function sourceIds($str){
		return Tool::objectToArray(json_decode($str));
	}

	/**
	* TEI source header filter
	*
	* @param string $str
	* @return string
	*/
	public static function sourceHeader($str){
		return $str;
	}

	/**
	* TEI abstract header filter
	*
	* @param string $str
	* @return string
	*/
	public static function sourceAbstract($str){
		return trim($str);
	}

	/**
	* TEI source toc filter
	*
	* @param string $str
	* @return string
	*/
	public static function sourceToc($str){
		return $str;
	}

	/**
	* Password filter
	*
	* Match password with 5-20 chars with letters and digits
	*
	* @param string $str
	* @return mixed
	*/
	public static function password($str){
		$re = '/
		# Match password with 5-20 chars with letters and digits
		^                # Anchor to start of string.
		(?=.*?[A-Za-z])  # Assert there is at least one letter, AND
		(?=.*?[0-9])     # Assert there is at least one digit, AND
		(?=.{5,20}\z)    # Assert the length is from 5 to 20 chars.
		/x';
		if (preg_match($re, $str))
			return $str;
		return null;
	}
}
?>
