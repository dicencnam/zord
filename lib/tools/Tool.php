<?php

/**
* Tool
* @package Zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Tool {

	/**
	* Object to Array
	*
	* @param object $object
	* @return array
	*/
	public static function objectToArray($object) {
		if(!is_object( $object ) && !is_array( $object ))
			return $object;
		if(is_object($object) )
			$object = get_object_vars( $object );
		return array_map(array('Tool', __FUNCTION__), $object );
	}

	/**
	* JSON encode
	*
	* @param array $val
	* @return string
	*/
	public static function json_encode($val) {
		$val = self::_json_encode($val);
		$val = preg_replace('#:"(\d+)"#s', ':$1' , $val);
		return $val;
	}

	private static function _json_encode($arr) {
		array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
		return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
	}

	/**
	* Get switcher Class (portal)
	*
	* @return object
	*/
	public static function getSwitcherClass(){
		return new $_SESSION['switcher']['name']();
	}

	/**
	* Clear note element in TEI string
	*
	* @param string $content TEI string
	* @param boolean $header Protect teiHeader element
	* @return string
	*/
	public static function clearNoteTEI($content){
		preg_match('#<teiHeader>(.*?)</teiHeader>#ms', $content, $matches);
		$content = preg_replace('#\s+<note\s+#s','<note ',$content);
		$content = preg_replace('#<teiHeader>(.*?)</teiHeader>#ms', $matches[0], $content);
		return $content;
	}


	/**
	* Clear note element in TEI string
	*
	* @param string $content TEI string
	* @param boolean $header Protect teiHeader element
	* @return string
	*/
	public static function clearTEI($content){
		$content = preg_replace('#\n#s',' ',$content);
		$content = preg_replace('#\s+#s',' ',$content);
		$content = preg_replace('#\s+<tei:note\s+#s','<tei:note ',$content);
		$content = preg_replace('#<tei:p>\s+#s','<tei:p>',$content);
		$content = preg_replace('#<tei:l>\s+#s','<tei:l>',$content);
		$content = preg_replace('#<tei:item>\s+#s','<tei:item>',$content);

		return $content;
	}

	/**
	* Create title
	*
	* @param array data
	* @return string
	*/
	public static function getTitle($data){
		$subtitle = '';
		if(isset($data['subtitle']))
			$subtitle = '. '.$data['subtitle'];
		$title = '';
		if(isset($data['title']))
			$title = $data['title'];

		if (strlen($title) > 40)
			$title = substr($title, 0, 40) . "…";

		return $title;
	}

	/**
	*  strip Non ASCII and '-' characters for domain name
	*
	* @param $name string
	* @return string
	*/
	public static function stripCharDomainName($name){
		return str_replace('-', '', preg_replace('/[^\x21-\x7E]/', '', $name));
	}
}
?>
