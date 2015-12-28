<?php
/**
* admin module - Out HTML
* @package zord
* @subpackage Module_admin
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Admin extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array('connect' => false);

	// Response -----------------------------------------------------------------
	public $response = 'HTML';

	// connexion -------------------------------------------------------------------
	public $connexion_filter = array();
	public $connexion_filter_path = array();
	/**
	* Page connexion
	*
	* @return HTML
	*/
	public function connexion() {
		Lang::load('admin');
		return Tpl::render('admin/connexion',Lang::get('admin'));
	}

	// Connect ------------------------------------------------------------------
	public $connect_filter = array(
		'login'   => FILTER_VALIDATE_EMAIL,
		'password' => FILTER_FLAG_NONE
	);
	public $connect_filter_path = array();
	/**
	* Check connexion
	*
	* @return HTML
	*/
	public function connect() {
		if( $this->check($this->request['params']['login'],trim($this->request['params']['password']) ) )
			return $this->redirection(array('module'=>'Adminconnect','action'=>'index'));
		return $this->connexion();
	}

	private function check($login, $password) {
		include_once(CONFIG_FOLDER.'config_db_admin.php');
		$user = ORM::for_table('admin', 'zord_admin')
			->where_raw('(`login` = ? AND `password` = ?)', array($login, hash('sha256', SALT.$password)))
			->find_one();
		if($user){
			//admin
			$data = $user->as_array();
			$_SESSION["admin"] = array(
				"id" => $data["id"],
				"login" => $data["login"]
			);
			$_SESSION["connect_admin"] = true;

			// appli user
			include(CONFIG_FOLDER.'config_portals.php');
			$Auth_user = new Auth_user();
			$Auth_user->userAssign(array(
				"id" => $data["id"],
				"login" => $data["login"],
				"name" => "ADMIN",
				"type" => "user",
				"level" => 1,
				"start" => '2000-01-01',
				"end" => '3000-01-01',
				"email" => $data["login"],
				"websites" => $websites,
				"ip" => '',
				"subscription" => '',
			),false);
			return true;
		}
		return false;
	}
}
?>
