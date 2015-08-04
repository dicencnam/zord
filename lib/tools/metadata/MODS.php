<?php
/**
* MODS
* @package zord
* @subpackage metadata
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class MODS {

	/**
	* xml
	*
	* @var array
	*/
	private $xml = array();

	/**
	* Convert special characters to XML entities
	*
	* @param string $val
	* @return string
	*/
	public function xmlspecialchars($val) {
		return trim(str_replace(array('&','>','<','"'), array('&#38;','&#62;','&#60;','&#34;'), $val));
	}

	/**
	* Set metadata
	*
	* @param array $metadata Book metadata
	*/
	public function setDATA($metadata){

		/*
		<typeOfResource>software, multimedia</typeOfResource>

		<location>
			<url displayLabel="electronic resource" usage="primary display">http://hdl.loc.gov/loc.music/collmus.mu000010</url>
		</location>

		<physicalDescription>
			<form authority="marcform">electronic</form>
			<form authority="gmd">electronic resource</form>
			<form>Computer data and programs.</form>
		</physicalDescription>
		*/


		$this->xml[] = '<typeOfResource>text</typeOfResource>'.PHP_EOL;
		$this->xml[] = '<genre authority="local">book</genre>'.PHP_EOL;
		$this->xml[] = '<genre authority="marcgt">book</genre>'.PHP_EOL;
		if(isset($metadata['title'])){
			$title = $metadata['title'];
			if(isset($metadata['subtitle']))
				$title .= '. '.$metadata['subtitle'];
			$this->xml[] = '<titleInfo><title>'.$this->xmlspecialchars($title).'</title></titleInfo>'.PHP_EOL;
		}

		if(isset($metadata['description']))
			$this->xml[] = '<abstract>'.$this->xmlspecialchars($metadata['description']).'</abstract>'.PHP_EOL;

		if(isset($metadata['publisher'])){
			$originInfo = '<originInfo>'.PHP_EOL;
			if(isset($metadata['pubplace']))
				$originInfo .= '<place><placeTerm type="text">'.$this->xmlspecialchars($metadata['pubplace']).'</placeTerm></place>'.PHP_EOL;

			$originInfo .= '<publisher>'.$this->xmlspecialchars($metadata['publisher']).'</publisher>'.PHP_EOL;

			if(isset($metadata['date'])){
				$date = explode('-', $metadata['date']);
				$originInfo .= '<copyrightDate>'.$date[0].'</copyrightDate>'.PHP_EOL;
			}
			$originInfo .= '<issuance>monographic</issuance>'.PHP_EOL;
			$originInfo .= '</originInfo>'.PHP_EOL;
			$this->xml[] = $originInfo;
		}

		if(isset($metadata['identifier_isbn']))
			$this->xml[] = '<identifier type="isbn">'.$this->xmlspecialchars($metadata['identifier_isbn']).'</identifier>'.PHP_EOL;

		if(isset($metadata['identifier_uri']))
			$this->xml[] = '<location><url usage="primary display">'.$metadata['identifier_uri'].'</url></location>'.PHP_EOL;

		if(isset($metadata['language']))
			$this->xml[] = '<language><languageTerm type="text">'.$metadata['language'].'</languageTerm></language>'.PHP_EOL;

		if(isset($metadata['rights']))
			$this->xml[] = '<accessCondition type="restrictionOnAccess">'.$this->xmlspecialchars($metadata['rights']).'</accessCondition>'.PHP_EOL;


		if(isset($metadata['relation'])){
			$relatedItem = '<relatedItem type="series">'.PHP_EOL;
			$relatedItem .= '<titleInfo><title>'.$this->xmlspecialchars($metadata['relation']).'</title></titleInfo>'.PHP_EOL;
			if(isset($metadata['collection_number']))
				$relatedItem .= '<part><detail type="volume"><number>'.$this->xmlspecialchars($metadata['collection_number']).'</number></detail></part>'.PHP_EOL;
			$relatedItem .= '</relatedItem>'.PHP_EOL;
			$this->xml[] = $relatedItem;
		}

		if(isset($metadata['pages']))
			$this->xml[] = '<physicalDescription><extent>'.$this->xmlspecialchars($metadata['pages']).' p.</extent></physicalDescription>'.PHP_EOL;


		if(isset($metadata['creator'])){
			$name = '';
			foreach($metadata['creator'] as $creator){
				$name .= '<name type="personal">'.PHP_EOL;
				$creator = explode(',',$creator);
				$name .= '<namePart type="family">'.$this->xmlspecialchars($creator[0]).'</namePart>'.PHP_EOL;
				if(isset($creator[1]))
					$name .= '<namePart type="given">'.$this->xmlspecialchars($creator[1]).'</namePart>'.PHP_EOL;
				$name .= '<role><roleTerm type="code" authority="marcrelator">aut</roleTerm></role>'.PHP_EOL.'</name>'.PHP_EOL;
			}
			$this->xml[] = $name;
		}

		if(isset($metadata['editor'])){
			$name = '';
			foreach($metadata['editor'] as $editor){
				$name .= '<name type="personal">'.PHP_EOL;
				$editor = explode(',',$editor);
				$name .= '<namePart type="family">'.$this->xmlspecialchars($editor[0]).'</namePart>'.PHP_EOL;
				if(isset($editor[1]))
					$name .= '<namePart type="given">'.$this->xmlspecialchars($editor[1]).'</namePart>'.PHP_EOL;
				$name .= '<role><roleTerm type="code" authority="marcrelator">edt</roleTerm></role>'.PHP_EOL.'</name>'.PHP_EOL;
			}

			$this->xml[] = $name;
		}

	}

	/**
	* Get XML
	*
	* @return string
	*/
	public function getDATA(){
		return '<mods>'.PHP_EOL.implode("\n",$this->xml).PHP_EOL.'</mods>';
	}
}
?>
