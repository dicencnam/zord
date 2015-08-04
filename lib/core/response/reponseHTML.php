<?php
/**
* HTML response
* @package Micro
* @subpackage Response
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
Class Response_HTML extends Response {

	/**
	* Response type
	*
	* @var String
	*/
	public $type = 'HTML';

	/**
	* Out content
	*
	* @var String
	*/
	public $content = '';

	/**
	* Out
	*/
	public function printOut(){
		$this->_headers['Content-Type'] = 'text/html;charset=UTF-8';
		$this->_headers['Vary'] = 'Accept';
		$this->sendHeaders();
		echo $this->content;
	}

	/**
	* Error
	*/
	public function error(){
		$this->content = Tpl::render('error/page',$this->statusCode((int) $this->content['code']));
		$this->printOut();
	}
}
?>
