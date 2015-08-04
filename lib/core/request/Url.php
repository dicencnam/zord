<?php
/**
* Interface IUrl
* @package Micro
* @subpackage Request
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
interface IUrl {
	public function getRequest();
}

/**
* URL mapping
* @package Micro
* @subpackage Request
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
abstract class Url {

	/**
	* Request
	*
	* @var Array
	*/
	protected $request = null;

	/**
	* Constructor
	*/
	public function __construct($realUrl=null) {

		$this->request = array(
			'method' => $_SERVER["REQUEST_METHOD"],
			'module' => MODULE_DEFAULT,
			'action' => ACTION_DEFAULT,
			'params_path' => array(),
			'params' => array()
		);

		if($realUrl==null){
			$realUrl = $this->_get_request_url();
			$this->request['params'] = array_merge($_GET, $_POST);
		}
		$this->request['url'] = $realUrl['url'];
		$this->request['sheme'] = $realUrl['sheme'];
		$parse = parse_url($realUrl['url']);
		$parse['path'] = substr($parse['path'], strlen(PROJECT_FOLDER));
		if($parse['path']!='')
			$this->request['params_path'] = explode('/',$parse['path']);
	}

	/**
	* Real URL
	*
	* @return Array
	*/
	private function _get_request_url(){
		$sheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
		return array(
			'sheme' => $sheme,
			'url' => $sheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
		);
	}
}

?>
