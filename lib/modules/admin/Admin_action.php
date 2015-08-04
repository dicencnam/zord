<?php
/**
* Admin_action module - Out JSON
* @package zord
* @subpackage Module_admin
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Admin_action extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array(
		'connect' => true,
		'name' => 'admin',
		'redirection' => array('module'=>'Admin','action'=>'connexion')
	);

	// Response -----------------------------------------------------------------
	public $response = 'JSON';

	// emptyCache ---------------------------------------------------------------
	public $emptyCache_filter = array();
	public $emptyCache_filter_path = array();
	/**
	* Delete cache
	*
	* @return JSON
	*/
	public function emptyCache() {
		Dirs::deleteContentsDirectory(substr(CACHE_FOLDER, 0, -1));
		Dirs::deleteContentsDirectory(TOOLS_FOLDER.'obfuscation'.DS.'shuffler');
		file_put_contents(TOOLS_FOLDER.'obfuscation'.DS.'shuffler'.DS.'.gitignore','*');
		include(LIB_FOLDER.'zord'.DS.'websites.php');
		foreach ($websites as $portal){
			@Dirs::deleteContentsDirectory(CSS_FOLDER.$portal.DS.'obf');
			@file_put_contents(CSS_FOLDER.$portal.DS.'obf'.DS.'.gitignore','*');
		}
		return array();
	}

	// getUsers ---------------------------------------------------------------
	public $getUsers_filter = array();
	public $getUsers_filter_path = array();
	/**
	* Get all users
	*
	* @return JSON
	*/
	public function getUsers() {
		return $this->_getUsers();
	}

	// --------------------------------------------------------------------------
	protected function _getUsers() {
		include_once(ROOT.'config'.DS.'config_orm_admin.php');
		$users = ORM::for_table('users', 'zord_admin')->find_array();
		$result = array();
		foreach ($users as $key => $value) {
			$result[] = array(
				'id' => $value['id'],
				'email' => $value['email'],
				'end' => $value['end'],
				'login' => $value['login'],
				'start' => $value['start'],
				'websites' => $value['websites'],
				'name' => $value['name'],
				'type' => $value['type']
			);
		}
		return array('content'=>$result);
	}

	// addUser ------------------------------------------------------------------
	public $addUser_filter = array(
		"users_email" => FILTER_VALIDATE_EMAIL,
		"users_end" => array(FILTER_VALIDATE_REGEXP,'options' => array('regexp','/^\d{4}-\d{2}-\d{2}$/')),
		"users_login" => FILTER_SANITIZE_STRING,
		"users_password" => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','password')),
		"users_start" => array(FILTER_VALIDATE_REGEXP,'options' => array('regexp','/^\d{4}-\d{2}-\d{2}$/')),
		"websites" => FILTER_SANITIZE_STRING,
		"users_type" => FILTER_SANITIZE_STRING,
		"users_level" => array(FILTER_VALIDATE_INT,'options' => array('min_range'=>0, 'max_range'=>1)),
		"users_name" => FILTER_SANITIZE_SPECIAL_CHARS
	);
	public $addUser_filter_path = array();
	/**
	* Add user
	*
	* @return JSON
	*/
	public function addUser() {
		if($this->request['params']['users_type']
			&& $this->request['params']['users_email']
			&& $this->request['params']['users_level']!=null
			&& $this->request['params']['users_end']
			&& $this->request['params']['users_login']
			&& $this->request['params']['users_password']
			&& $this->request['params']['websites']
			&& $this->request['params']['users_name']
			&& $this->request['params']['users_start']){
				include_once(ROOT.'config'.DS.'config_orm_admin.php');
				$user = ORM::for_table('users','zord_admin')->create();
				$user->set('type', $this->request['params']['users_type']);
				$user->set('email', $this->request['params']['users_email']);
				$user->set('end', $this->request['params']['users_end']);
				$user->set('password', hash('sha256', SALT.$this->request['params']['users_password']));
				$user->set('login', $this->request['params']['users_login']);
				$user->set('start', $this->request['params']['users_start']);
				$user->set('name', $this->request['params']['users_name']);
				$user->set('websites', $this->request['params']['websites']);
				$user->set('level', $this->request['params']['users_level']);
				$user->save();
				return $this->_getUsers();
		}
		$invalid = '';

		if($this->request['params']['users_password']==null)
			$invalid = 'users_password_invalid';
		if($this->request['params']['users_start']==null)
			$invalid = 'users_start_invalid';
		if($this->request['params']['users_end']==null)
			$invalid = 'users_end_invalid';
		if($this->request['params']['users_email']==null)
			$invalid = 'users_email_invalid';
		return $this->error(303,$invalid);
	}
	// updateUser ------------------------------------------------------------------
	public $updateUser_filter = array(
		"email" => FILTER_VALIDATE_EMAIL,
		"end" => array(FILTER_VALIDATE_REGEXP,'options' => array('regexp','/^\d{4}-\d{2}-\d{2}$/')),
		"login" => FILTER_SANITIZE_STRING,
		"id" => FILTER_VALIDATE_INT,
		"websites" => FILTER_SANITIZE_STRING,
		"start" => array(FILTER_VALIDATE_REGEXP,'options' => array('regexp','/^\d{4}-\d{2}-\d{2}$/')),
		"name" => FILTER_SANITIZE_SPECIAL_CHARS
	);
	public $updateUser_filter_path = array();
	/**
	* Update user
	*
	* @return JSON
	*/
	public function updateUser() {
		if($this->request['params']['id']
		&& $this->request['params']['email']
		&& $this->request['params']['end']
		&& $this->request['params']['login']
		&& $this->request['params']['websites']
		&& $this->request['params']['name']
		&& $this->request['params']['start']){
			include_once(ROOT.'config'.DS.'config_orm_admin.php');
			$user = ORM::for_table('users', 'zord_admin')
			->where('id', $this->request['params']['id'])
			->find_one();

			if($user){
				$user->email = $this->request['params']['email'];
				$user->end = $this->request['params']['end'];
				$user->login = $this->request['params']['login'];
				$user->start = $this->request['params']['start'];
				$user->websites = $this->request['params']['websites'];
				$user->name = $this->request['params']['name'];
				$user->save();
				return $this->_getUsers();
			}
			return $this->error(303,'Invalid');
		}
		$invalid = '';

		if($this->request['params']['users_start']==null)
			$invalid = 'users_start_invalid';
		if($this->request['params']['users_end']==null)
			$invalid = 'users_end_invalid';
		if($this->request['params']['users_email']==null)
			$invalid = 'users_email_invalid';
		return $this->error(303,$invalid);
	}

	// delUser ---------------------------------------------------------------
	public $delUser_filter = array(
		'id' => FILTER_VALIDATE_INT
	);
	public $delUser_filter_path = array();
	/**
	* Delete user
	*
	* @return JSON
	*/
	public function delUser() {
		if($this->request['params']['id']){
			include_once(ROOT.'config'.DS.'config_orm_admin.php');
			$user = ORM::for_table('users', 'zord_admin')
				->where('id', $this->request['params']['id'])
				->find_one();
			$user->delete();
			return $this->_getUsers();
		}
		return $this->error(303,'Invalid');
	}

	// getCounterDatas ---------------------------------------------------------------
	public $getCounterDatas_filter = array();
	public $getCounterDatas_filter_path = array();
	/**
	* Get counter all data
	*
	* @return JSON
	*/
	public function getCounterDatas() {
		$solr = new Solr();
		$users = $this->_getUsers();
		return array(
			'users' => $users['content'],
			'books' => $solr->getBooksTitle('library_s:book')
		);
	}

	// getCounterUserRapport_2 --------------------------------------------------
	public $getCounterUserRapport_2_filter = array(
		"user" => FILTER_VALIDATE_INT,
		"start" => array(FILTER_VALIDATE_REGEXP,'options' => array('regexp','/^\d{4}-\d{2}$/')),
		"end" => array(FILTER_VALIDATE_REGEXP,'options' => array('regexp','/^\d{4}-\d{2}$/'))
	);
	public $getCounterUserRapport_2_filter_path = array();
	/**
	* Get counter user - rapport 2
	*
	* @return JSON
	*/
	public function getCounterUserRapport_2() {
		// list of file
		$counter = new Counter();
		return $counter->getReport_2(
			$this->request['params']['user'],
			$this->request['params']['start'],
			$this->request['params']['end']
		);
	}

	// getCounterUserRapport_5 --------------------------------------------------
	public $getCounterUserRapport_5_filter = array(
		"user" => FILTER_VALIDATE_INT,
		"start" => array(FILTER_VALIDATE_REGEXP,'options' => array('regexp','/^\d{4}-\d{2}$/')),
		"end" => array(FILTER_VALIDATE_REGEXP,'options' => array('regexp','/^\d{4}-\d{2}$/'))
	);
	public $getCounterUserRapport_5_filter_path = array();
	/**
	* Get counter user - rapport 5
	*
	* @return JSON
	*/
	public function getCounterUserRapport_5() {
		// list of file
		$counter = new Counter();
		return $counter->getReport_5(
			$this->request['params']['user'],
			$this->request['params']['start'],
			$this->request['params']['end']
		);
	}

	// getEpubs ---------------------------------------------------------------
	public $getEpubs_filter = array();
	public $getEpubs_filter_path = array();
	/**
	* Get all ePubs
	*
	* @return JSON
	*/
	public function getEpubs() {
		$solr = new Solr();
		$epubs = Dirs::globRecursive(EPUBS_FOLDER.'*');
		$books = $solr->getBooksTitle('library_s:book');

		$namePortal = '';
		$_Epubs = array();
		foreach($epubs as $key => $value){
			if(is_dir($value)){
				$namePortal = pathinfo($value,PATHINFO_FILENAME);
				$_Epubs[$namePortal] = array();
			} else {
				$_Epubs[$namePortal][] = pathinfo($value,PATHINFO_FILENAME);
			}
		}

		$noEpubs = array();
		foreach($books as $portal => $bookInPortal){
			foreach($bookInPortal as $isbn => $title){
				if(!isset($_Epubs[$portal]))
					$_Epubs[$portal] = array();
				if(!in_array($isbn,$_Epubs[$portal])){
					if(!isset($noEpubs[$portal]))
						$noEpubs[$portal] = array();
					$noEpubs[$portal][$isbn] = $title;
				}
			}
		}
		return array(
			'books' => $noEpubs
		);
	}



	// createEpubs ---------------------------------------------------------------
	public $createEpubs_filter = array(
		'books' => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','json'))
	);
	public $createEpubs_filter_path = array();
	/**
	* Create ePub
	*
	* @return JSON
	*/
	public function createEpubs() {
		$books = Tool::objectToArray($this->request['params']['books']);
		foreach($books as $portal => $bookInPortal){
			foreach($bookInPortal as $isbn){
				$cmd = 'nohup nice -n 10 /usr/bin/php -f '.ROOT.'services/services.php teitoepub '.$isbn.' '.$portal.' >> '.LOGS_FOLDER.'epubs_'.date("Y-m-d").'.log &';
				shell_exec($cmd);
			}
		}
		return array();
	}

	// getDocs ---------------------------------------------------------------
	public $getDocs_filter = array();
	public $getDocs_filter_path = array();
	/**
	* Get all documents (books)
	*
	* @return JSON
	*/
	public function getDocs() {
		$solr = new Solr();
		$books = $solr->getBooksTitle('library_s:book');
		return array(
			'books' => $books
		);
	}

	// delBook ---------------------------------------------------------------
	public $delBook_filter = array(
		'books' => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','json'))
	);
	public $delBook_filter_path = array();
	/**
	* Delete book
	*
	* @return JSON
	*/
	public function delBook() {
		$books = Tool::objectToArray($this->request['params']['books']);
		$solr = new Solr();
		foreach($books as $portal => $bookInPortal){
			foreach($bookInPortal as $isbn){
				$solr->delBook($isbn,$portal);
				if($isbn!=''){
					$docFolder = TEI_FOLDER.$portal.DS.$isbn;
					if(is_dir($docFolder) && $docFolder != TEI_FOLDER.$portal.DS)
						Dirs::deleteDirectory($docFolder);
				}
			}
		}
		return array();
	}

	// portalNew ---------------------------------------------------------------
	public $portalNew_filter = array(
		"portal" => FILTER_SANITIZE_STRING,
		"url" => FILTER_VALIDATE_URL,
		"publisher" => FILTER_SANITIZE_STRING
	);
	public $portalNew_filter_path = array();
	/**
	* Create a new portal
	*
	* @return JSON
	*/
	public function portalNew() {
		if($this->request['params']['portal'] && $this->request['params']['url'] && $this->request['params']['publisher']){
			$Portal = new Portal();
			$Portal->create(
				$this->request['params']['portal'],
				$this->request['params']['url'],
				$this->request['params']['publisher']
			);
			return array();
		}
		return $this->error(303,'Invalid');
	}

	// portalNew ---------------------------------------------------------------
	public $portalDel_filter = array(
		"portal" => FILTER_SANITIZE_STRING,
		"url" => FILTER_VALIDATE_URL
	);
	public $portalDel_filter_path = array();
	/**
	* Create a new portal
	*
	* @return JSON
	*/
	public function portalDel() {
		if($this->request['params']['portal'] && $this->request['params']['url']){
			$Portal = new Portal();
			$Portal->delete(
				$this->request['params']['portal'],
				$this->request['params']['url']
			);
			return array();
		}
		return $this->error(303,'Invalid');
	}

	// emailNews ---------------------------------------------------------------
	public $emailNews_filter = array();
	public $emailNews_filter_path = array();
	/**
	* email News
	*
	* @return JSON
	*/
	public function emailNews() {
		// $mail = new Mail();
		// $sourceMail = 'server@team-story.com';
		//
		//
		// $targetMail = 'david.dauvergne@gmail.com';
		//
		// $subject = "test Mail";
		//
		// $message = '<h1>message en html</h1>';
		//
		// $mail = new Mail();
		// $mail->CharSet = "UTF-8";
		// $mail->SetFrom($sourceMail, $this->_baseUrl['domaine']);
		// $mail->AddReplyTo($sourceMail, $this->_baseUrl['domaine']);
		// $mail->AddAddress($targetMail, "");
		// $mail->Subject = $subject;
		// $mail->MsgHTML('<html><head><meta http-equiv="Content-type" content="text/html; charset=utf-8"/></head><body>'.$message.'</body></html>');
		// $mail->Send();


		return array();
		// return $this->error(303,'Invalid');
	}
}
?>
