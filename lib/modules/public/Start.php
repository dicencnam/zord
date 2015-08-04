<?php
/**
* Start module - Out HTML
* @package zord
* @subpackage Module_client
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Start extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array('connect' => false);

	// Response -----------------------------------------------------------------
	public $response = 'HTML';

	// index --------------------------------------------------------------------
	public $index_filter = array();
	public $index_filter_path = array();
	/**
	* Home
	*
	* @return HTML
	*/
	public function index() {
		$cl = Tool::getSwitcherClass();
		return $cl->getStart();
	}

	// page --------------------------------------------------------------------
	public $page_filter = array();
	public $page_filter_path = array();
	/**
	* Get page
	*
	* @return HTML
	*/
	public function page() {
		if(isset($this->request['page'])){
			$cl = Tool::getSwitcherClass();
			return $cl->getPage($this->request['page']);
		} else {
			return $this->redirection(array('module'=>'Start','action'=>'index'));
		}
	}

	// disconnect ---------------------------------------------------------------
	public $disconnect_filter = array();
	public $disconnect_filter_path = array();
	/**
	* Disconnect
	*
	* @return HTML
	*/
	public function disconnect() {
		// prevent reload disconnect
		if(isset($_SESSION["user"]) && isset($_SESSION["user"]['websites']) ){
			foreach($_SESSION["user"]['websites'] as $website)
				unset($_SESSION['connect_user_'.$website]);
			unset($_SESSION['user']);
			$_SESSION['level'] = 0;
		}
		return $this->redirection(array('module'=>MODULE_DEFAULT,'action'=>ACTION_DEFAULT));
	}

	// connexion ---------------------------------------------------------------
	public $connexion_filter = array(
		'lasthref' => FILTER_VALIDATE_URL
	);
	public $connexion_filter_path = array();
	/**
	* Connexion page
	*
	* @return HTML
	*/
	public function connexion($lasthref=null) {
		if($lasthref==null)
			$lasthref = $this->request['params']['lasthref'];
		$cl = Tool::getSwitcherClass();
		return $cl->getConnexion($lasthref);
	}

	// Connect ------------------------------------------------------------------
	public $connect_filter = array(
		'login'   => FILTER_SANITIZE_STRING,
		'password' => FILTER_FLAG_NONE,
		'lasthref' => FILTER_VALIDATE_URL
	);
	public $connect_filter_path = array();
	/**
	* Check connect
	*
	* @return HTML
	*/
	public function connect() {
		$Auth_user = new Auth_user();
		if( $Auth_user->checkUser($this->request['params']['login'],trim($this->request['params']['password']) ) ){
			if($this->request['params']['lasthref']){
				// redirection from last request
				$url = array(
					'sheme' => parse_url($this->request['params']['lasthref'], PHP_URL_SCHEME),
					'url' => $this->request['params']['lasthref']
				);
				$requestClName = REQUEST;
				$requestIns = new $requestClName($url);
				$request = $requestIns->parseRequest();
				return $this->redirection($request);
			} else{
				return $this->redirection(array('module'=>'Start','action'=>'index'));
			}
		}
		return $this->connexion($this->request['params']['lasthref']);
	}
}
?>
