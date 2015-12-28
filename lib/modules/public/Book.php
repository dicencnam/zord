<?php
/**
* Book module - Out HTML
* @package zord
* @subpackage Module_client
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Book extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array(
		'connect' => true,
		'name' => '',
		'redirection' => array('module'=>'Book_notconnect','action'=>'show')
	);

	public function __construct() {
		$this->auth['name'] = 'user_'.$_SESSION['switcher']['name'];
	}

	// Response -----------------------------------------------------------------
	public $response = 'HTML';

	// show -------------------------------------------------------------------
	public $show_filter = array();
	public $show_filter_path = array();
	/**
	* Book page - connected
	*
	* @return HTML
	*/
	public function show() {
		$file = TEI_FOLDER.$_SESSION['switcher']['name'].DS.$this->request['book'];

		/*
		BOOK LEVEL =< USER LEVEL
		0 0 OK
		1 0 NO
		0 1 OK
		1 1 OK
		*/
		if(file_exists($file)){
			$metadata = Tool::objectToArray(json_decode(file_get_contents($file.DS.'header.json')));
			if($metadata['level']<=$_SESSION['level']){
				if($metadata['level']==0)
					Counter::setReport_2($_SESSION['user']['id'],$this->request['book'],$this->request['part']);
				$cl = Tool::getSwitcherClass();
				if(Subscription::validBook($this->request['book']))
					$html = $cl->getBook($this->request['book'],$this->request['part'],$metadata['level'],Tool::getTitle($metadata));
				else
					$html = $cl->getBook($this->request['book'],null,$metadata['level'],Tool::getTitle($metadata));
				return $html;
			}
		}
		return $this->error(404,'');
	}
}
?>
