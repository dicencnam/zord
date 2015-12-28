<?php
/**
* Adminconnect module - Out HTML
* @package zord
* @subpackage Module_admin
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Adminconnect extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array(
		'connect' => true,
		'name' => 'admin',
		'redirection' => array('module'=>'Admin','action'=>'connexion')
	);

	// Response -----------------------------------------------------------------
	public $response = 'HTML';

	// Index -------------------------------------------------------------------
	public $index_filter = array();
	public $index_filter_path = array();
	/**
	* Page administration
	*
	* @return HTML
	*/
	public function index() {
		Lang::load('adminconnect');

		// listes des websites
		include(CONFIG_FOLDER.'config_portals.php');

		return Tpl::render('admin/page',array(
			'websites' => json_encode($websites),
			'websitesURL' => json_encode($websitesURL),
			'lang' => Lang::get('adminconnect')
		));
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
		$_SESSION = array();
		session_destroy();
		return $this->redirection(array('module'=>MODULE_DEFAULT,'action'=>ACTION_DEFAULT));
	}

	// liseuse ---------------------------------------------------------------
	public $liseuse_filter = array(
		'epub' => FILTER_SANITIZE_NUMBER_INT,
		'portal' => FILTER_SANITIZE_STRING
	);
	public $liseuse_filter_path = array();
	/**
	* liseuse
	*
	* @return HTML
	*/
	public function liseuse() {
		$epubName = $this->request['params']['epub'];
		$portal = $this->request['params']['portal'];

		if(!file_exists(EPUBSADMIN_FOLDER))
			mkdir(EPUBSADMIN_FOLDER,0777);

		$epubsDir = EPUBSADMIN_FOLDER.DS;
		Dirs::clearTempDirectory($epubsDir,2000);

		$epubFolder = $epubsDir.$epubName;

		if(file_exists($epubFolder))
			Dirs::deleteDirectory($epubFolder);

		mkdir($epubFolder,0777);
		$epubFile = EPUBS_FOLDER.$portal.DS.$epubName.'.epub';
		$zip = new ZipArchive;
		if ($zip->open($epubFile) === TRUE) {
				$zip->extractTo($epubFolder.DS);
				$zip->close();
		}
		if(file_exists($epubFolder.DS.'mimetype'))
			return Tpl::render('admin/liseuse',array('name'=> $epubName));
		else
			return $this->error(404,'');
	}
}
?>
