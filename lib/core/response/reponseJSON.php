<?php
/**
* JSON response
* @package Micro
* @subpackage Response
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
Class Response_JSON extends Response {

	/**
	* Response type
	*
	* @var String
	*/
	public $type = 'JSON';

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
		$this->_headers['Content-Type'] = 'application/json';
		$json = Tool::json_encode($this->content);
		$this->_httpHeaders['Content-length'] = strlen($json);
		$this->sendHeaders();
		echo $json;
	}

	/**
	* Error
	*/
	public function error(){
		$this->statusCode((int) $this->content['code']);
		$this->content = array(
			'message' => $this->content['message']
		);
		$this->printOut();
	}
}
?>
