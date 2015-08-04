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
		include(LIB_FOLDER.'zord'.DS.'websites.php');
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
}
?>
