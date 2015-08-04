<?php
/**
* Simple URL mapping
* @package Micro
* @subpackage Request
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class SimpleUrl extends Url implements IUrl {

	private $_domain = '';
	/**
	* Request mapping
	*
	* @return Array
	*/
	public function getRequest(){
		// switcher
		$domain = array_reverse(explode('.', $_SERVER['HTTP_HOST']));
		if(!isset($domain[1]))
			$domain[1] = 'localhost';
		if(!isset($domain[2]))
			$domain[2] = 'www';

		// Domain name
		$this->_domain = Tool::stripCharDomainName($domain[2].'_'.$domain[1]);

		if(!isset($_SESSION['domaine_name']) || $_SESSION['domaine_name']!=$this->_domain){
			$this->_loadPortal($this->_domain);
		}
		return $this->parseRequest();
	}

	public function _loadPortal($name){
		include(LIB_FOLDER.'zord'.DS.'websites.php');
		if(array_key_exists($name, $websitesDomain)) {
			$portal = $websitesDomain[$name];
		} else {
			$portal = $websites[0];
		}
		$_SESSION['domaine_name'] = $this->_domain;
		$_SESSION['switcher'] = array(
			'name' => $portal,
			'url' => $websitesURL[$portal]
		);
	}

	/**
	* Request parse
	*
	* @return Array
	*/
	public function parseRequest(){
		if(count($this->request['params_path'])>0){
			if(is_numeric($this->request['params_path'][0])){
				$this->request['module'] = 'Book';
				$this->request['action'] = 'show';
				$this->request['book'] = filter_var(array_shift($this->request['params_path']), FILTER_SANITIZE_NUMBER_INT);
				if (count($this->request['params_path']) > 0)
					$this->request['part'] = filter_var(array_shift($this->request['params_path']), FILTER_SANITIZE_STRING);
				else
					$this->request['part'] = '';
			} else {

				if($this->request['params_path'][0]=='page'){
					array_shift($this->request['params_path']);
					$this->request['module'] = 'Start';
					$this->request['action'] = 'page';
					if (count($this->request['params_path']) > 0)
						$this->request['page'] = filter_var(array_shift($this->request['params_path']), FILTER_SANITIZE_STRING);
				} else if($this->request['params_path'][0]=='portal'){
					array_shift($this->request['params_path']);
					if (count($this->request['params_path']) > 0){
						$name = filter_var(array_shift($this->request['params_path']), FILTER_SANITIZE_STRING);
						$this->_loadPortal($name);
						$this->request['module'] = 'Portalswitch';
						$this->request['action'] = 'index';
					}
				} else {
					$this->request['module'] = filter_var(array_shift($this->request['params_path']), FILTER_SANITIZE_STRING);
					if (count($this->request['params_path']) > 0)
						$this->request['action'] = filter_var(array_shift($this->request['params_path']), FILTER_SANITIZE_STRING);
				}
			}
		}

		if(isset($this->request['params']['module'])){
			$this->request['module'] = filter_var($this->request['params']['module'], FILTER_SANITIZE_STRING);
			unset($this->request['params']['module']);
		}
		if(isset($this->request['params']['action'])){
			$this->request['action'] = filter_var($this->request['params']['action'], FILTER_SANITIZE_STRING);
			unset($this->request['params']['action']);
		}

		return $this->request;
	}
}
?>
