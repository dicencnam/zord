<?php
/**
* JS
* @package Micro
* @subpackage Tools
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class JsCss {

	/**
	* JS variables
	*
	* @var String
	*/
	protected $value = '';

	/**
	* JS scripts
	*
	* @var String
	*/
	protected $script = '';

	/**
	* CSS links
	*
	* @var String
	*/
	protected $link = '';

	/**
	* Set JS value
	*
	* @param string $type Type of value (string,integer,json,object)
	* @param string $varName Variable name
	* @param string $value
	*/
	public function setValue($type,$varName,$value){
		if($type=='mixed')
			$type = gettype($value);

		switch($type){
			case 'string':
				$this->value .= "\tvar ".$varName." = '".$value."';".PHP_EOL;
			break;
			case 'integer':
			case 'json':
				$this->value .= "\tvar ".$varName." = ".$value.";".PHP_EOL;
			break;
			case 'NULL':
				$this->value .= "\tvar ".$varName." = null;".PHP_EOL;
			break;
			case 'boolean':
				$v = ($value) ? 'true' : 'false';
				$this->value .= "\tvar ".$varName." = ".$v.";".PHP_EOL;
			break;
			case 'array':
				$this->value .= "\tvar ".$varName." = ".Tool::json_encode($value).";".PHP_EOL;
			break;
		}
	}

	/**
	* Set CSS link
	*
	* @param string $name CSS file name (no extension and relative link)
	* @param string $media Media type (screen,print...)
	*/
	public function setLink($name,$media='screen'){
		$this->link .= "\t".'<link rel="stylesheet" type="text/css" media="'.$media.'" href="'.BASEURL.'public/css/'.$_SESSION['switcher']['name'].'/'.$name.'.css"/>'.PHP_EOL;
	}

	/**
	* Set JS script
	*
	* @param string $name JS file name (no extension and relative link)
	*/
	public function setScript($name){
		$this->script .= "\t".'<script type="text/javascript" src="'.BASEURL.$name.'.js"></script>'.PHP_EOL;
	}

	/**
	* Get HTML header for JS and CSS
	*
	* @return string
	*/
	public function get(){
		if($this->value!='')
			$this->value = '<script language="Javascript">'.PHP_EOL.$this->value."\t</script>".PHP_EOL;
		return $this->value.$this->script.$this->link;
	}
}
?>
