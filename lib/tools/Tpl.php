<?php
/**
* Template
* @package Micro
* @subpackage Tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Tpl {

	/**
	* Render template
	*
	* @param string $nameTpl Template name
	* @param array $v Values
	* @return string
	*/
	public static function render($nameTpl,$v=null){
		$file = VIEW_FOLDER.$nameTpl.'.php';
		ob_start ();
		include($file);
		return ob_get_clean();
	}
}
?>
