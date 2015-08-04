<?php
/**
* Motor module - Out BINARY
* @package zord
* @subpackage Module_client
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Motor extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array('connect' => false);

	// Response -----------------------------------------------------------------
	public $response = 'BINARY';

	// index --------------------------------------------------------------------
	public $index_filter = array();
	public $index_filter_path = array();
	/**
	* Create/load Sitemap
	*
	* @return BINARY
	*/
	public function index() {
		$cl = Tool::getSwitcherClass();
		return array(
			'extension' => 'xml',
			'content' => $cl->getMotor()
		);
	}

	// getCitationsFile --------------------------------------------------------------------
	public $getCitationsFile_filter = array();
	public $getCitationsFile_filter_path = array();
	/**
	* Load citations export
	*
	* @return BINARY
	*/
	public function getCitationsFile() {
		return array(
			'extension' => 'doc',
			'filename' => 'citations_'.$_SESSION['switcher']['name'].'_'.date("Y-m-d").'.doc',
			'content' => $_SESSION['citations']
		);
	}

	// getNotices --------------------------------------------------------------------
	public $getNotices_filter = array();
	public $getNotices_filter_path = array();
	/**
	* Get Notices
	*
	* @return BINARY
	*/
	public function getNotices() {
/* 		$content = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'
<collection xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.loc.gov/MARC21/slim" xsi:schemaLocation="http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd">';*/

$content = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'
<modsCollection xmlns="http://www.loc.gov/mods/v3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-2.xsd">'.PHP_EOL;

		foreach ($_SESSION['notices'] as $value) {
			// $file_dc = TEI_FOLDER.$_SESSION['switcher']['name'].DS.$value.DS.'dc.xml';
			// if(!file_exists($file_dc)){
			// 	$header = TEI_FOLDER.$_SESSION['switcher']['name'].DS.$value.DS.'header.json';
			// 	$metadata = Tool::objectToArray(json_decode(file_get_contents($header)));
			// 	$dc = new DC_xml();
			// 	$dc->set($metadata);
			// 	file_put_contents($file_dc,$dc->get());
			// }
			// $doc = new DOMDocument();
			// $xsl = new XSLTProcessor();
			// // $doc->load(LIB_FOLDER.'xslt'.DS.'DC2MARC21slim.xsl');
			// $doc->load(LIB_FOLDER.'xslt'.DS.'DC_MODS3-5_XSLT1-0.xsl');
			// $xsl->importStyleSheet($doc);
			// $doc->load($file_dc);
			// $content .= PHP_EOL.preg_replace('#<record xmlns="http\://www\.loc\.gov/MARC21/slim">#','<record>',$xsl->transformToXML($doc));




			$header = TEI_FOLDER.$_SESSION['switcher']['name'].DS.$value.DS.'header.json';
			$mods = new MODS;
			$mods->setDATA(Tool::objectToArray(json_decode(file_get_contents($header))));
			$content .= $mods->getDATA();
		}
		$content .= '</modsCollection>';

		if($_SESSION['notices_format'] == 'mods'){
			return array(
				'extension' => 'xml',
				'filename' => $_SESSION['switcher']['name'].'_mods_'.date("Y-m-d").'.xml',
				'content' => $content
			);
		}
		$doc = new DOMDocument();
		$xsl = new XSLTProcessor();
		$doc->load(LIB_FOLDER.'xslt'.DS.'MODS3-4_MARC21slim_XSLT1-0.xsl');
		$xsl->importStyleSheet($doc);
		$doc->loadXML($content);
		$marcxml = $xsl->transformToXML($doc);

		return array(
			'extension' => 'xml',
			'filename' => $_SESSION['switcher']['name'].'_marcxml_'.date("Y-m-d").'.xml',
			'content' => $marcxml
		);

	}
}
?>
