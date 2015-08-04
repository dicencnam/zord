<?php
/**
* Start module - Out JSON
* @package zord
* @subpackage Module_client
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Search extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array('connect' => false);

	// Response -----------------------------------------------------------------
	public $response = 'JSON';

	// index --------------------------------------------------------------------
	public $index_filter = array(
		'query' => FILTER_FLAG_NONE,
		'book' => FILTER_VALIDATE_INT
	);
	public $index_filter_path = array();
	/**
	* Search query
	*
	* @return JSON
	*/
	public function index() {
		$solr = new Solr();
		if($this->request['params']['book']!=null && $_SESSION["CONNECT"])
			Counter::setReport_5($_SESSION['user']['id'],$this->request['params']['book']);
		$query = Tool::objectToArray(json_decode($this->request['params']['query']));
		$query['query'][] = array('repository_s'=>$_SESSION['switcher']['name']);
		$response = $solr->search($query);
		return array('content'=>$response);
	}

	// csl --------------------------------------------------------------------
	public $csl_filter = array('book' => FILTER_VALIDATE_INT);
	public $csl_filter_path = array();
	/**
	* CSL - Citation Style Language for à book
	*
	* @return JSON
	*/
	public function csl() {
		$file = TEI_FOLDER.$_SESSION['switcher']['name'].DS.$this->request['params']['book'].DS.'header.json';
		if(file_exists($file)){
			$metadata = Tool::objectToArray(json_decode(file_get_contents($file)));
			$CSL_json = new CSL_json('csl_'.uniqid());
			$CSL_json->setDATA($metadata);
			return array('content' => $CSL_json->getDATA());
		}
		return $this->error(303,'No file');
	}

	// lang --------------------------------------------------------------------
	public $lang_filter = array('lang' => FILTER_SANITIZE_STRING);
	public $lang_filter_path = array();
	/**
	* Change lang
	*
	* @return JSON
	*/
	public function lang() {
		include(LIB_FOLDER.'zord'.DS.'zordLangs.php');
		if(in_array($this->request['params']['lang'],$zordLangs))
			$_SESSION['___LANG___'] = $this->request['params']['lang'];

		return array();
	}

	// citation ----------------------------------------------------------------
	public $createCitations_filter = array('content' => FILTER_FLAG_NONE);
	public $createCitations_filter_path = array();
	/**
	* Create citation for import
	*
	* @return JSON
	*/
	public function createCitations() {
		$cl = Tool::getSwitcherClass();
		$_SESSION['citations'] = $cl->exportCitation($this->request['params']['content']);
		return array();
	}

	// crossDoc ----------------------------------------------------------------
	public $getCrossDoc_filter = array(
		'id' => FILTER_SANITIZE_STRING,
		'book' => FILTER_SANITIZE_STRING,
		'part' => FILTER_SANITIZE_STRING
	);
	public $getCrossDoc_filter_path = array();
	/**
	* Get URL for crossDoc
	*
	* @return JSON
	*/
	public function getCrossDoc() {
		$_urlEls = explode( ':', str_replace('#','',$this->request['params']['id']) );
		$dir = TEI_FOLDER.$_SESSION['switcher']['name'].DS.$_urlEls[0];
		if (file_exists($dir)){
			$ids = Tool::objectToArray(json_decode(file_get_contents($dir.DS.'ids.json')));
			if (array_key_exists($_urlEls[1], $ids))
				return array('book'=>$_urlEls[0],'part'=>$ids[$_urlEls[1]],'anchor'=>$_urlEls[1]);
		}
		// log error
		$msg = 'Error book:'.$this->request['params']['id'].' part:'.$this->request['params']['part'].' target:'.$this->request['params']['id'];
		Log::set($msg,'crossDoc');
		return $this->error(303,'No file');
	}

	// noticesSetIDS ----------------------------------------------------------------
	public $noticesSetIDS_filter = array(
		'format' => FILTER_SANITIZE_STRING,
		'ids' => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','json'))
	);
	public $noticesSetIDS_filter_path = array();
	/**
	* Notices set documents ID
	*
	* @return JSON
	*/
	public function noticesSetIDS() {
		$_SESSION['notices_format'] = $this->request['params']['format'];
		$_SESSION['notices'] = Tool::objectToArray($this->request['params']['ids']);
		return array();
	}

	// bugSave --------------------------------------------------------------------
	public $bugSave_filter = array(
		'bug' => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','json'))
	);
	public $bugSave_filter_path = array();
	/**
	* bugSave - Citation Style Language for à book
	*
	* @return JSON
	*/
	public function bugSave() {
		$bug = Tool::objectToArray($this->request['params']['bug']);
		$msg = "\nURL : ".$bug['zord_url']."\n";
		$msg .= "PAGE : ".$bug['page']."\n";
		$msg .= "CITATION : ".$bug['zord_citation']."\n";
		$msg .= "NOTE : ".$bug['zord_note']."\n";
		$msg .= "-------------------------------------------------\n";
		Log::set($msg,'bug');
		return array();
	}
}
?>
