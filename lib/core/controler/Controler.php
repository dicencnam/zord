<?php
/**
* Interface IControler
* @package Micro
* @subpackage Controler
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
interface IControler {
	public function route();
}

/**
* Controler
* @package Micro
* @subpackage Controler
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
abstract class Controler {

	/**
	* Redirection
	*/
	protected function _redirection(){
		$action = null;
		if(isset($this->request['module'])){
			if(Autoloader::classExit($this->request['module'])){
				$module = new $this->request['module']();
				if( method_exists($module,$this->request['action']) )
					$action = $this->request['action'];
			}
		}

		if($action){
			// Filters
			$filter = $action.'_filter';
			$this->request['params'] = filter_var_array($this->request['params'], $module->$filter);
			$filter_path = $action.'_filter_path';

			$_params_path = @array_combine(array_keys($module->$filter_path), $this->request['params_path']);
			if($_params_path===false)
				$_params_path = array();

			$this->request['params_path'] = filter_var_array($_params_path, $module->$filter_path);

			// Auth
			$validAuth = false;
			if($module->auth['connect']){
				if(isset($_SESSION['connect_'.$module->auth['name']]))
					$validAuth = true;
			} else {
				$validAuth = true;
			}

			// valid auth
			if($validAuth){
				$module->request = $this->request;
				// response
				$response = $this->_Response($module->response);
				$module->response = $response;
				$response->content = $module->$action();
				if(is_array($response->content) && isset($response->content['__redirection__']) ){
						foreach ($response->content['options'] as $key => $value)
							$this->request[$key] = $value;
						$this->_redirection();
				} else if(is_array($response->content) && isset($response->content['__error__']) ){
					$response->error();
				} else {
					$response->printOut();
				}
			} else {
				if(isset($module->auth['redirection'])){
					$this->request['module'] = $module->auth['redirection']['module'];
					$this->request['action'] = $module->auth['redirection']['action'];
				} else {
					$this->request = $this->_getDefaultRoute();
				}
				$this->_redirection();
			}
		} else {
			$this->request = $this->_getDefaultRoute();
			$this->_redirection();
		}
	}

	/**
	* Default route
	*
	* @return Array
	*/
	protected function _getDefaultRoute(){
		return array(
			'module' => MODULE_DEFAULT,
			'action' => ACTION_DEFAULT,
			'params' => array(),
			'params_path' => array()
		);
	}

	/**
	* Response
	*
	* @param String $type Response type
	* @return Abject
	*/
	protected function _Response($type){
		$class = 'Response_'.strtoupper($type);
		$response = new $class();
		return $response;
	}
}
?>
