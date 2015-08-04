<?php
/**
* Start module - Out BINARY
* @package zord
* @subpackage Module_client
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Services extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array('connect' => false);

	// Response -----------------------------------------------------------------
	public $response = 'BINARY';

	// unapi --------------------------------------------------------------------
	public $unapi_filter = array(
		'id' => FILTER_VALIDATE_INT,
		'format' => FILTER_SANITIZE_STRING,
	);
	public $unapi_filter_path = array();
	/**
	* UNAPI - UNAPI XML or bibliontology RDF XML
	*
	* @return BINARY
	*/
	public function unapi() {
		if($this->request['params']['id']){
			$file = TEI_FOLDER.$_SESSION['switcher']['name'].DS.$this->request['params']['id'].DS.'header.json';

			if(file_exists($file)){
				$metadata = Tool::objectToArray(json_decode(file_get_contents($file)));
				if($this->request['params']['format']=='rdf_bibliontology'){
					$rdf = new RDF_bibliontology($this->request['params']['id']);
					$rdf->setDATA($metadata);
					return array(
						'extension' => 'rdf',
						'content' => $rdf->getDATA()
					);
				}
			}
		}

		$xml = '<?xml version="1.0" encoding="UTF-8"?><formats><format name="rdf_bibliontology" type="application/rdf+xml" docs="http://bibliontology.com/" /></formats>';

		return array(
			'extension' => 'xml',
			'content' => $xml
		);
	}
}
?>
