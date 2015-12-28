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
		include(CONFIG_FOLDER.'config_portals.php');
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
		include_once(CONFIG_FOLDER.'config_db_admin.php');
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
				'type' => $value['type'],
				'ip' => $value['ip'],
				'level' => $value['level'],
				'subscription' => $value['subscription']
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
		"users_name" => FILTER_SANITIZE_SPECIAL_CHARS,
		"users_subscription" => array(FILTER_VALIDATE_INT,'options' => array('min_range'=>0, 'default'=>0)),
		"users_ip" => FILTER_SANITIZE_STRING
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
			&& $this->request['params']['users_subscription']>=0
			&& $this->request['params']['users_start']){
				include_once(CONFIG_FOLDER.'config_db_admin.php');
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
				$user->set('subscription', $this->request['params']['users_subscription']);
				if($this->request['params']['users_type']=='IP')
					$user->set('ip', $this->request['params']['users_ip']);
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
		if($this->request['params']['users_subscription']==null)
			$invalid = 'users_subscription_invalid';
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
		"name" => FILTER_SANITIZE_SPECIAL_CHARS,
		"subscription" => array(FILTER_VALIDATE_INT,'options' => array('min_range'=>0, 'default'=>0)),
		"ip" => FILTER_SANITIZE_STRING
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
		&& $this->request['params']['subscription']>=0
		&& $this->request['params']['start']){
			include_once(CONFIG_FOLDER.'config_db_admin.php');
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
				$user->subscription = $this->request['params']['subscription'];
				if($user->type=='IP')
					$user->ip = $this->request['params']['ip'];
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
		if($this->request['params']['users_subscription']==null)
			$invalid = 'users_subscription_invalid';
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
			include_once(CONFIG_FOLDER.'config_db_admin.php');
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
				$pathinfo = pathinfo($value);
				if($pathinfo['extension']=='epub'){
					$namePortal = basename($pathinfo['dirname']);
					if(!isset($_Epubs[$namePortal]))
						$_Epubs[$namePortal] = array();
					$_Epubs[$namePortal][] = $pathinfo['filename'];
				}
			}
		}
		return array(
			'books' => $books,
			'epubs' => $_Epubs
		);
	}



	// createEpubs ---------------------------------------------------------------
	public $createEpubs_filter = array(
		'book' => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','json'))
	);
	public $createEpubs_filter_path = array();
	/**
	* Create ePub
	*
	* @return JSON
	*/
	public function createEpubs() {
		$book = Tool::objectToArray($this->request['params']['book']);
		$folder = TEI_FOLDER.$book['portal'].DS.$book['id'].DS;
		$partJSONFile = $folder.'part_save.json';
		$files = Tool::objectToArray(json_decode(file_get_contents($partJSONFile)));
		$content = array();
		foreach ($files as $file) {
			$content[] = array(
				'title' => $file['title'],
				'file' => $file['file'],
				'tei' => file_get_contents($folder.$file['file'].'.xml')
			);
		}
		return array('content'=>$content,'navMap'=>file_get_contents($folder.'toc.xml'));
	}

	// saveEpubs ---------------------------------------------------------------
	public $saveEpubs_filter = array(
		'data' => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','sourceStucture'))
	);
	public $saveEpubs_filter_path = array();
	/**
	* Save ePub
	*
	* @return JSON
	*/
	public function saveEpubs() {
		$data = $this->request['params']['data'];
		$portal = $data['portal'];
		$id = $data['id'];

		if(Filter::validPortal($portal) && Filter::validEAN13($id)){

			// portal folder
			if(!file_exists(EPUBS_FOLDER.$portal))
				mkdir(EPUBS_FOLDER.$portal,0777);

			$epubFile = EPUBS_FOLDER.$portal.DS.$id.'.epub';
			if(file_exists($epubFile))
				unlink($epubFile);

			$metadata = Tool::objectToArray(json_decode(file_get_contents(TEI_FOLDER.$portal.DS.$id.DS.'header.json')));

			$Epub = new Epub($epubFile,$metadata);

			// cover ------------------------------------------------------------------------
			$coverIMG = PROFILES_FOLDER.$portal.DS.'epub'.DS.'cover.jpg';
			$coverExtension = 'jpg';
			if(file_exists(COVERS_FOLDER.$id.'.jpg'))
				$coverIMG = COVERS_FOLDER.$id.'.jpg';
			else if(file_exists(COVERS_FOLDER.$id.'.png')){
				$coverIMG = COVERS_FOLDER.$id.'.png';
				$coverExtension = 'png';
			}
			$Epub->addCover($coverIMG,$coverExtension);

			// html --------------------------------------------------------------------------
			foreach ($data['content'] as $key => $value) {
				$name = explode('_',$value['file']);
				$Epub->addXHTML(
					$value['tei'],
					$name[1],
					array(
						'spine' 	=> array(
							'position'	=> null,
							'linear'		=> 'yes'
						),
						'guide' 	=> array(
							'position'	=> null,
							'title'			=> $value['title'],
							'type'			=> 'text'
						)
					)
				);
			}

			// medias -------------------------------------------------------------------------
			foreach ($data['medias'] as $target) {
				$source = str_replace('medias/', MEDIA_FOLDER.$id.DS, $target);
				$Epub->addSource($source,$target);
			}

			// NCX ----------------------------------------------------------------------------
			$Epub->addNCX($data['navMap']);

			// CSS ----------------------------------------------------------------------------
			$cssFile = PROFILES_FOLDER.$portal.DS.'epub'.DS.'epub.css';
			$Epub->addSource(PROFILES_FOLDER.$portal.DS.'epub'.DS.'epub.css','css/epub.css');

			$cl = new $portal();
			$cl->epub($Epub);

			$Epub->finish();

			// epubcheck
			Cmd::epubcheck($portal,$id);
		}

		return array();
	}

	// getEpubCheck ---------------------------------------------------------------
	public $getEpubCheck_filter = array(
		'book' => FILTER_SANITIZE_NUMBER_INT,
		"portal" => FILTER_SANITIZE_STRING
	);
	public $getEpubCheck_filter_path = array();
	/**
	* Get epubCheck
	*
	* @return JSON
	*/
	public function getEpubCheck() {
		$book = $this->request['params']['book'];
		$portal = $this->request['params']['portal'];
		if(Filter::validPortal($portal) && Filter::validEAN13($book)) {
			return array(
				'content' => htmlspecialchars(file_get_contents(EPUBS_FOLDER.$portal.DS.$book.'_check.txt'))
			);
		} else {
			return array(
				'content' => htmlspecialchars('Error portal or book name')
			);
		}
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
				if(Filter::validPortal($portal) && Filter::validEAN13($isbn)){
					$solr->delBook($isbn,$portal);
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
		"publisher" => FILTER_SANITIZE_STRING,
		"portalname" => FILTER_SANITIZE_STRING
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
				$this->request['params']['publisher'],
				$this->request['params']['portalname']
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
		if(Filter::validPortal($this->request['params']['portal']) && $this->request['params']['url']){
			$Portal = new Portal();
			$Portal->delete(
				$this->request['params']['portal'],
				$this->request['params']['url']
			);
			return array();
		}
		return $this->error(303,'Invalid');
	}
}
?>
