<?php
/**
* CSL_json Citation Style Language JSON
* @package zord
* @subpackage metadata
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class CSL_json {

	/**
	* Citation type
	*
	* @var array
	*/
	private $bib = array("type" => "book");

	/**
	* Constructor
	*
	* @param String $id Book ID
	*/
	public function __construct($id){
		$this->bib['id'] = $id;
	}

	/**
	* Set metadata
	*
	* @param array $metadata Book metadata
	*/
	public function setDATA($metadata){
		if(isset($metadata['title'])){
			// TODO subtitle
			$this->bib['title'] = $metadata['title'];
			if(isset($metadata['subtitle']))
				$this->bib['title'] .= '. '.$metadata['subtitle'];
		}

		if(isset($metadata['publisher']))
			$this->bib['publisher'] = $metadata['publisher'];


		if(isset($metadata['date'])){
			$this->bib['issued'] = array("date-parts"=>array(array($metadata['date'])));
		}

		if(isset($metadata['language'])){
			if(strlen($metadata['language']))
				$this->bib['language'] = ISO639::code3ToCode2($metadata['language']);
			else
				$this->bib['language'] = $metadata['language'];
		}

		if(isset($metadata['identifier_isbn']))
			$this->bib['ISBN'] = '"'.$metadata['identifier_isbn'].'"';

		if(isset($metadata['identifier_uri']))
			$this->bib['zord_URL'] = $metadata['identifier_uri'];

		if(isset($metadata['pubplace']))
			$this->bib['publisher-place'] = $metadata['pubplace'];



/*

"original-date": {
								"$ref": "date-variable"
						},

		if(isset($metadata['type']))
			$this->bibo->set('dcterms:type', $metadata['type']);

		if(isset($metadata['rights']))
			$this->bibo->set('dcterms:rights', $metadata['rights']);

		if(isset($metadata['format']))
			$this->bibo->set('dcterms:format', $metadata['format']);

		if(isset($metadata['relation']))
			$this->bibo->set('dcterms:relation', $metadata['relation']);
*/
		if(isset($metadata['creator'])){
			$this->bib['author'] = array();
			foreach($metadata['creator'] as $creator){
				$creator = explode(',',$creator);
				$person = array('family' => trim($creator[0]) );
				if(isset($creator[1]))
					$person['given'] = trim($creator[1]);
				$this->bib['author'][] = $person;
			}
		}
		if(isset($metadata['editor'])){
			$this->bib['editor'] = array();
			foreach($metadata['editor'] as $editor){
				$editor = explode(',',$editor);
				$person = array('family' => trim($editor[0]) );
				if(isset($editor[1]))
					$person['given'] = trim($editor[1]);
				$this->bib['editor'][] = $person;
			}
		}
	}

	/**
	* Get RDF XML
	*
	* @return string
	*/
	public function getDATA(){
		return $this->bib;
	}
}
?>
