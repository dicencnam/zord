<?php
/**
* DC_xml Dublin Core XML
* @package zord
* @subpackage metadata
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class DC_epub extends Meta {

	// Title -----------------------------------------------------------------------
	/**
	* Set title data - Dublin Core XML
	*
	* @param String $value
	*/
	public function title($value){
		$this->_dc .= "\t\t<dc:title>".$this->xmlspecialchars($value).'</dc:title>'.PHP_EOL;
	}

	// Creator ---------------------------------------------------------------------
	/**
	* Set creator data - Dublin Core XML
	*
	* @param String $value
	*/
	public function creator($value){
		foreach($value as $creator)
			$this->_dc .= "\t\t<dc:creator>".$this->xmlspecialchars($creator).'</dc:creator>'.PHP_EOL;
	}
	// Contributor -----------------------------------------------------------------
	// editor ---------------------------------------------------------------------
	/**
	* Set editor data - Dublin Core XML
	*
	* @param String $value
	*/
	public function editor($value){
		foreach($value as $editor)
			$this->_dc .= "\t\t<dc:contributor>".$this->xmlspecialchars($editor).'</dc:contributor>'.PHP_EOL;
	}

	// Subject ---------------------------------------------------------------------
	/**
	* Set subject data - Dublin Core XML
	*
	* @param String $value
	*/
	public function subject($value){
		$this->_dc .= "\t\t<dc:subject>".$this->xmlspecialchars($value).'</dc:subject>'.PHP_EOL;
	}
	// Description -----------------------------------------------------------------
	/**
	* Set description data - Dublin Core XML
	*
	* @param String $value
	*/
	public function description($variable){
		foreach ($variable as $key => $value) {
			$this->_dc .= "\t\t<dc:description xml:lang=\"$key\">".$this->xmlspecialchars($value).'</dc:description>'.PHP_EOL;
		}
	}
	// Publisher -------------------------------------------------------------------
	/**
	* Set publisher data - Dublin Core XML
	*
	* @param String $value
	*/
	public function publisher($value){
		$this->_dc .= "\t\t<dc:publisher>".$this->xmlspecialchars($value).'</dc:publisher>'.PHP_EOL;
	}

	// Format ----------------------------------------------------------------------
	/**
	* Set format data - Dublin Core XML
	*
	* @param String $value
	*/
	public function format($value){
		$this->_dc .= "		<dc:format>".$this->xmlspecialchars($value).'</dc:format>'.PHP_EOL;
	}

	// Source ----------------------------------------------------------------------
	/**
	* Set source data - Dublin Core XML
	*
	* @param String $value
	*/
	public function source($value){
		$this->_dc .= "\t\t<dc:source>".$this->xmlspecialchars($value).'</dc:source>'.PHP_EOL;
	}
	// Coverage --------------------------------------------------------------------
	// Rights ----------------------------------------------------------------------
	/**
	* Set rights data - Dublin Core XML
	*
	* @param String $value
	*/
	public function rights($value){
		$this->_dc .= "\t\t<dc:rights>".$this->xmlspecialchars($value).'</dc:rights>'.PHP_EOL;
	}
	// Audience --------------------------------------------------------------------
	// Date ------------------------------------------------------------------------
	/**
	* Set date data - Dublin Core XML
	*
	* @param String $value
	*/
	public function date($value){
		$date = explode('-', $value);
		$this->_dc .= "\t\t<dc:date opf:event=\"original-publication\">".$date[0].'</dc:date>'.PHP_EOL;
	}
	// Language ------------------------------------------------------------------------
	/**
	* Set language data - Dublin Core XML
	*
	* @param String $value
	*/
	public function language($value){
		$this->_dc .= "\t\t<dc:language>".$this->xmlspecialchars($value).'</dc:language>'.PHP_EOL;
	}

	// Relation ------------------------------------------------------------------------
	/**
	* Set relation data - Dublin Core XML
	*
	* @param String $value
	*/
	public function relation($value){
		$this->_dc .= "\t\t<dc:relation>".$this->xmlspecialchars($value).'</dc:relation>'.PHP_EOL;
	}

	// Identifier ------------------------------------------------------------------------
	/**
	* Set identifier ISBN data - Dublin Core XML
	*
	* @param String $value
	*/
	public function identifier_isbn($value){
		$this->_dc .= "\t\t<dc:identifier opf:scheme=\"ISBN\">".$this->xmlspecialchars($value).'</dc:identifier>'.PHP_EOL;
	}

	/**
	* Set identifier URI data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function identifier_uri($value){
		$this->_dc .= "\t\t<dc:identifier opf:scheme=\"URI\">".$this->xmlspecialchars($value).'</dc:identifier>'.PHP_EOL;
	}

	/**
	* Get data string
	*
	* @return string
	*/
	public function get(){
		return PHP_EOL."\t\t<dc:type>text</dc:type>".PHP_EOL.$this->_dc;
	}
}
?>
