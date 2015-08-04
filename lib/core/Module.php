<?php
/**
* Abstract class Module
* @package Micro
* @subpackage Core
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
abstract class Abs_module {

	/**
	* Magic method __call
	*
	* @param String $method Method name
	* @param Array $args Arguments
	* @return Mixed
	*/
	public function __call($method, $args) {
		return call_user_func_array(array(&$this, $method), $args);
	}
}

/**
* Abstract class Module extend
* @package Micro
* @subpackage Module
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
abstract class Module extends Abs_module {

	/**
	* Redirection
	*
	* @param String $module Module name
	* @param String $action Method name
	* @return Array
	*/
	public function redirection($options=array()){
		if(count($options)==0)
			$options = array('module'=>MODULE_DEFAULT,'action'=>ACTION_DEFAULT);
		return array(
			'__redirection__' => true,
			'options' => $options
		);
	}

	/**
	* Error
	*
	* @param Integer $code Error code
	* @param String $message Error message
	* @return Array
	*/
	public function error($code,$message){
		return array(
			'__error__' => true,
			'code' => $code,
			'message' => $message
		);
	}
}
?>
