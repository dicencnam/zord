<?php
/**
* Meta_html Meta elements for HTML
* @package zord
* @subpackage metadata
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Meta_html extends Meta {

	/**
	* Set title data - Meta element for HTML
	*
	* @param String $value
	*/
	public function title($value){
		$this->_dc .= '	<title>'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'</title>'.PHP_EOL;
	}

	/**
	* Set creator data - Meta element for HTML
	*
	* @param String $value
	*/
	public function creator($value){
		foreach($value as $creator)
			$this->_dc .= '	<meta name="author" content="'.htmlspecialchars($creator, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}

	/**
	* Set generator data - Meta element for HTML
	*
	* @param String $value
	*/
	public function generator($value){
		$this->_dc .= '	<meta name="generator" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}

	/**
	* Set rights data - Meta element for HTML
	*
	* @param String $value
	*/
	public function rights($value){
		$this->_dc .= '	<meta name="copyright" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}

	/**
	* Set subject data - Meta element for HTML
	*
	* @param String $value
	*/
	public function subject($value){
		$this->_dc .= '	<meta name="keywords" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}

	/**
	* Set description data - Meta element for HTML
	*
	* @param String $value
	*/
	public function description($variable){
		if(gettype($variable)=='string'){
			$this->_dc .= '	<meta name="description" content="'.htmlspecialchars($variable, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
		} else {
			foreach ($variable as $key => $value) {
				$this->_dc .= '	<meta name="description" xml:lang="'.$key.'" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
			}
		}
	}

	public function ref_url($value){
		$this->_dc .= '	<meta name="ref_url" content="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"/>'.PHP_EOL;
	}


}
?>
