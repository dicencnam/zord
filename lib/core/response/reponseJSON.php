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
		$json = $this->_json_encode($this->content);
		$json = preg_replace('#:"(\d+)"#s', ':$1' , $json);
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

	/**
	* JSON UTF-8 encode
	*
	* @return string
	*/
	private function _json_encode($arr){
		array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
		return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
	}
}
?>
