<?php
/**
* DC_html Dublin Core for HTML
* @package zord
* @subpackage metadata
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class DC_html extends Meta {

	/**
	* Constructor
	*/
	public function __construct(){
		$this->_dc .= '<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />'.PHP_EOL;
	}

	// Title -----------------------------------------------------------------------
	/**
	* Set title data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function title($value){
		$this->_dc .= '	<meta name="DC.title" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}

	// Creator ---------------------------------------------------------------------
	/**
	* Set creator data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function creator($value){
		foreach($value as $creator)
			$this->_dc .= '	<meta name="DC.creator" content="'.htmlspecialchars($creator, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}
	// Contributor -----------------------------------------------------------------
	// editor ---------------------------------------------------------------------
	/**
	* Set editor data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function editor($value){
		foreach($value as $editor)
			$this->_dc .= '	<meta name="DC.contributor" content="'.htmlspecialchars($editor, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}

	// Subject ---------------------------------------------------------------------
	/**
	* Set subject data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function subject($value){
		$this->_dc .= '	<meta name="DC.subject" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}
	// Description -----------------------------------------------------------------
	/**
	* Set description data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function description($variable){
		if(gettype($variable)=='string'){
			$this->_dc .= '	<meta name="DC.description" content="'.htmlspecialchars($variable, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
		} else {
			foreach ($variable as $key => $value) {
				$this->_dc .= '	<meta name="DC.description" xml:lang="'.$key.'" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
			}
		}
	}
	// Publisher -------------------------------------------------------------------
	/**
	* Set publisher data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function publisher($value){
		$this->_dc .= '	<meta name="DC.publisher" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}

	// Type ------------------------------------------------------------------------
	/**
	* Set type data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function type($value){
		$this->_dc .= '	<meta name="DC.type" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}

	// Format ----------------------------------------------------------------------
	/**
	* Set format data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function format($value){
		$this->_dc .= '	<meta name="DC.format" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}

	// Source ----------------------------------------------------------------------
	/**
	* Set source data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function source($value){
		$this->_dc .= '	<meta name="DC.source" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}
	// Coverage --------------------------------------------------------------------
	// Rights ----------------------------------------------------------------------
	/**
	* Set rights data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function rights($value){
		$this->_dc .= '	<meta name="DC.rights" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}
	// Audience --------------------------------------------------------------------
	// Date ------------------------------------------------------------------------
	/**
	* Set date data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function date($value){
		$this->_dc .= '	<meta name="DC.date" scheme="W3CDTF" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}
	// Language ------------------------------------------------------------------------
	/**
	* Set language data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function language($value){
		$this->_dc .= '	<meta name="DC.language" scheme="RFC3066" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}

	// Relation ------------------------------------------------------------------------
	/**
	* Set relation data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function relation($value){
		$this->_dc .= '	<meta name="DC.relation.isPartOf" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}

	// Identifier ------------------------------------------------------------------------
	/**
	* Set identifier ISBN data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function identifier_isbn($value){
		$this->_dc .= '	<meta name="DC.identifier" scheme="ISBN" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}

	/**
	* Set identifier URI data - Dublin Core for HTML
	*
	* @param String $value
	*/
	public function identifier_uri($value){
		$this->_dc .= '	<meta name="DC.identifier" scheme="URI" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}
}
?>
