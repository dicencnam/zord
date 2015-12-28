<?php
/**
* RDF_bibliontology
* @package zord
* @subpackage metadata
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class RDF_bibliontology extends EasyRdf_Graph {

	/**
	* Book id
	*
	* @var String
	*/
	private $id = null;

	/**
	* Bibo object
	*
	* @var object EasyRdf_Graph
	*/
	private $bibo = null;

	/**
	* Constructor
	*
	* @param String $id Book ID
	*/
	public function __construct($id){
		$this->id = $id;
		$this->bibo = $this->resource(BASEURL.$id, 'bibo:Book');
	}

	/**
	* Set metadata
	*
	* @param array $metadata Book metadata
	*/
	public function setDATA($metadata){
		if(isset($metadata['title'])){
			$title = $metadata['title'];
			if(isset($metadata['subtitle']))
				$title .= '. '.$metadata['subtitle'];
			$this->bibo->set('dcterms:title', $title);
		}

		if(isset($metadata['description'])){
			if(gettype($metadata['description'])=='string'){
				$this->bibo->set('dcterms:abstract', $metadata['description']);
			} else {
				foreach ($metadata['description'] as $key => $value)
					$this->bibo->addLiteral('dcterms:abstract', $value, $key);
			}
		}

		if(isset($metadata['publisher'])){
			$Organization = $this->newBnode('foaf:Organization');
			if(isset($metadata['pubplace']))
				$Organization->set('address:localityName', $metadata['pubplace']);
			$Organization->set('foaf:name', $metadata['publisher']);
			$this->bibo->set('dcterms:publisher', $Organization);
		}


		if(isset($metadata['date'])){
			$date = explode('-', $metadata['date']);
			$this->bibo->set('dcterms:date', $date[0]);
		}
		if(isset($metadata['language']))
			$this->bibo->set('dcterms:language', $metadata['language']);

		if(isset($metadata['identifier_isbn']))
			$this->bibo->set('bibo:isbn13', $metadata['identifier_isbn']);

		if(isset($metadata['identifier_uri']))
			$this->bibo->set('bibo:uri', $metadata['identifier_uri']);

		if(isset($metadata['type']))
			$this->bibo->set('dcterms:type', $metadata['type']);

		if(isset($metadata['rights']))
			$this->bibo->set('dcterms:rights', $metadata['rights']);

		if(isset($metadata['format']))
			$this->bibo->set('dcterms:format', $metadata['format']);

		if(isset($metadata['relation'])){
				$biboSeries = $this->newBnode('bibo:Series');
				$biboSeries->set('dcterms:title', trim($metadata['relation']));
				if(isset($metadata['collection_number']))
					$biboSeries->set('bibo:number', trim($metadata['collection_number']));
				$this->bibo->set('dcterms:isPartOf', $biboSeries);
		}

		if(isset($metadata['pages']))
			$this->bibo->set('bibo:numPages', $metadata['pages']);


		if(isset($metadata['creator'])){
			foreach($metadata['creator'] as $creator){
				$creator = explode(',',$creator);
				$Person = $this->newBnode('foaf:Person');
				$Person->set('foaf:surname', trim($creator[0]));
				if(isset($creator[1]))
					$Person->set('foaf:givenname', trim($creator[1]));
				$this->bibo->add('dcterms:creator', $Person);
			}
		}

		if(isset($metadata['editor'])){
			foreach($metadata['editor'] as $editor){
				$editor = explode(',',$editor);
				$Person = $this->newBnode('foaf:Person');
				$Person->set('foaf:surname', trim($editor[0]));
				if(isset($editor[1]))
					$Person->set('foaf:givenname', trim($editor[1]));
				$this->bibo->add('bibo:editor', $Person);
			}
		}

	}

	/**
	* Get RDF XML
	*
	* @return string
	*/
	public function getDATA(){
		$ser = $this->serialise('rdfxml');
		$ser = preg_replace('#/creatorx/#s','/creator/',$ser);
		return $ser;
	}
}
?>
