<?php
/**
* Admin_binary module - Out BINARY
* @package zord
* @subpackage Module_admin
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Admin_binary extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array(
		'connect' => true,
		'name' => 'admin',
		'redirection' => array('module'=>'Admin','action'=>'connexion')
	);

	// Response -----------------------------------------------------------------
	public $response = 'BINARY';


	// getTEIfile --------------------------------------------------------------------
	public $getTEIfile_filter = array();
	public $getTEIfile_filter_path = array(
		'repository' => FILTER_SANITIZE_STRING,
		'book' => FILTER_SANITIZE_NUMBER_INT
	);
	/**
	* Get TEI file
	*
	* @return BINARY
	*/
	public function getTEIfile() {
		$book = $this->request['params_path']['book'];
		$repository = $this->request['params_path']['repository'];
		return array(
			'extension' => 'xml',
			'filename' => $book.'.xml',
			'content' => file_get_contents(TEI_FOLDER.$repository.DS.$book.DS.$book.'.xml')
		);
	}
}
?>
