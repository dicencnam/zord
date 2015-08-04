<?php
/**
* Book module - Out HTML
* @package zord
* @subpackage Module_client
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Book_notconnect extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array('connect' => false);

	// Response -----------------------------------------------------------------
	public $response = 'HTML';

	// show -------------------------------------------------------------------
	public $show_filter = array();
	public $show_filter_path = array();
	/**
	* Book page - not connect
	*
	* @return HTML
	*/
	public function show() {
		$file = TEI_FOLDER.$_SESSION['switcher']['name'].DS.$this->request['book'];
		if(file_exists($file)){
			$metadata = Tool::objectToArray(json_decode(file_get_contents($file.DS.'header.json')));
			if($metadata['level']<=$_SESSION['level']){
				$cl = Tool::getSwitcherClass();
				$html = $cl->getBook($this->request['book'],null,$metadata['level'],Tool::getTitle($metadata));
				return $html;
			}
		}
		return $this->error(404,'');
	}
}
?>
