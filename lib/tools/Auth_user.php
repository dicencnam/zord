<?php
/**
* Auth_user
* @package zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Auth_user {

	/**
	* Constructor
	*/
	public function __construct(){
		include_once(ROOT.'config'.DS.'config_orm_user.php');
	}

	/**
	* Check IP
	*/
	public function checkIp() {
		$_SESSION["IPCONNEXION"] = false;
		$_SESSION["CONNECT"] = false;
		if($IP = $this->_getIp() && !MODE_ADMIN){
			$IP = explode('.',$IP);
			$users = ORM::for_table('users', 'zord_user')
			->where('type', 'IP')
			->find_array();
			if($users){
				foreach ($users as $key => $value) {
					$IPList = explode(',',$value['login']);
					foreach ($IPList as $v) {
						$userIP = explode('.',$v);
						if($this->_validIP($IP,$userIP)){
							$this->_validUser($users[$key]);
							$_SESSION["IPCONNEXION"] = true;
							$_SESSION["CONNECT"] = true;
						}
					}
				}
			}
		}

		$_SESSION["IPCHECK"] = true;
	}

	/**
	* Check user
	*
	* @param String $login Login
	* @param String $password Password
	* @return boolean
	*/
	public function checkUser($login, $password) {
		$user = ORM::for_table('users', 'zord_user')
		->where_raw('(`login` = ? AND `password` = ?)', array($login, hash('sha256', SALT.$password)))
		->find_one();
		if($user)
			return $this->_validUser($user->as_array());
		return FALSE;
	}

	/**
	* User data assign
	*
	* @param array $data
	*/
	public function userAssign($data) {
		session_regenerate_id();
		$_SESSION["user"] = array(
			"id" => $data["id"],
			"login" => $data["login"],
			"name" => $data['name'],
			"type" => $data['type'],
			"start" => $data['start'],
			"end" => $data['end'],
			"email" => $data['email'],
			"level" => $data['level'],
			"websites" => $data['websites']
		);
		$_SESSION['level'] = (int) $data['level'];

		$_SESSION["CONNECT"] = true;
		// login for websites
		foreach($data['websites'] as $website)
			$_SESSION['connect_user_'.$website] = true;
	}

	/**
	* Validate IP
	*
	* @param String $IP Actual IP
	* @param String $userIP User IP
	* @return boolean
	*/
	protected function _validIP($IP,$userIP) {
		return min(array_map(
			function($n,$m){
				if($n===$m || $m==='*')
					return 1;
				return 0;
			},$IP,$userIP));
	}

	/**
	* Get IP
	*
	* @return String
	*/
	protected function _getIp() {
		return filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
	}

	/**
	* Validate User
	*
	* @param array $data User data
	* @return boolean
	*/
	protected function _validUser($data) {
		// website
		$data['websites'] = explode(',', trim($data['websites']));
		// dates
		$now = new DateTime( date("Y-m-d") );
		$now = $now->format('Ymd');
		$expire = new DateTime($data['end']);
		$expire = $expire->format('Ymd');

		if (in_array($_SESSION['switcher']['name'], $data['websites']) && $expire>$now) {
			$this->userAssign($data);
			return true;
		}
		return false;
	}
}
?>
