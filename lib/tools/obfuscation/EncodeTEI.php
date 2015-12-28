<?php
/**
* EncodeTEI obfuscation
* @package zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class EncodeTEI {

	/**
	* Shuffler directory
	*
	* @var String
	*/
	protected $_shufflerDir = null;

	/**
	* TEI elements
	*
	* @var array
	*/
	protected $_elements = null;

	/**
	* TEI attributes
	*
	* @var array
	*/
	protected $_attributes = null;

	/**
	* Obfuscation name
	*
	* @var array
	*/
	protected $_name = null;

	/**
	* TEI prefix
	*
	* @var array
	*/
	protected $_prefix = null;

	/**
	* Position for alphanumeric translate
	*
	* @var array
	*/
	private $_pos = 0;

	/**
	* Elements in JS
	*
	* @var array
	*/
	protected $_els = array(
		'pb' => array('n','ed'),
		'lb' => array('ed'),
		'graphic' => array('url'),
		'note' => array('place','n'),
		'ref' => array(),
		'head' => array(),
		'emph' => array(),
		'p' => array(),
		'table' => array(),
		'head' => array(),
		'row' => array(),
		'cell' => array('cols','rows'),
		'facsimile' => array(),
		'figure' => array('rend'),
		'ref' => array('target','creferencing','rendition'),
		'hi' => array('rend'),
	);

	/**
	* Constructor
	*/
	public function __construct(){
		$this->_shufflerDir = dirname(__file__).DIRECTORY_SEPARATOR.'shuffler'.DIRECTORY_SEPARATOR;
		// level acces if is admin no encode
		if($_SESSION['level']==0){
			$files = glob($this->_shufflerDir.'*.php', 0);
			if(count($files)<OBFUSCATION_MODELS_MAX){
				if(!file_exists(CSS_FOLDER.$_SESSION['switcher']['name'].DS.'obf'))
					mkdir(CSS_FOLDER.$_SESSION['switcher']['name'].DS.'obf',0777);
				$characters = 'abcdefghijklmnopqrstuvwxyz';
				$this->_prefix = $characters[rand(0, 25)];
				// p5 => 546 elements & 245 attributes
				include(dirname(__file__).DIRECTORY_SEPARATOR.'elements_tei.php');

				// elements convert
				shuffle($TEI['elements']);
				$this->_elements = array();
				foreach($TEI['elements'] as $element)
					$this->_elements[strtolower($element)] = $this->_num2alpha();

				// attributes convert
				shuffle($TEI['attributes']);
				$this->_attributes = array();
				$this->_pos = 0;
				foreach($TEI['attributes'] as $attr){
					if (strpos($attr, ':') === false)// no xml:xxx
						$this->_attributes[strtolower($attr)] = $this->_num2alpha();
				}
				$this->_name = md5(json_encode($this->_elements).json_encode($this->_attributes));
				$content = "<?php\n\$this->_elements = ".var_export($this->_elements, true).";\n";
				$content .= "\$this->_attributes = ".var_export($this->_attributes, true).";\n";
				$content .= "\$this->_name = '".$this->_name."';\n";
				$content .= "\$this->_prefix = '".$this->_prefix."';\n?>";
				file_put_contents($this->_shufflerDir.$this->_name.'.php',$content);
				// json
				file_put_contents($this->_shufflerDir.$this->_name.'.txt',$this->creatELS($this->_prefix));
			} else {
				shuffle($files);
				include($files[0]);
			}
		}
	}

	/**
	* Get TEI XML
	*
	* @param String $file File of TEI
	* @return string
	*/
	public function getXML($file){
		$xml = file_get_contents($file);
		if($_SESSION['level']==1){
			return $xml;
		}
		$xml = preg_replace('#<\?xml.*>#','',$xml);
		$xml = preg_replace_callback('#<(\/*)tei:(\w+)([\s|>])#si', array($this, 'replaceElement'), $xml);
		$xml = preg_replace_callback('#\s(\w+)="#si', array($this, 'replaceAttribute'), $xml);
		return preg_replace('# xmlns:tei="http://www.tei-c\.org/ns/1\.0"#',' xmlns:'.$this->_prefix.'="http://zord.org/1.0"',$xml);
	}

	/**
	* Get TEI CSS
	*
	* @param String $portal Portal name
	* @return string CSS relative link
	*/
	public function getCSS($portal){
		if($_SESSION['level']==1)
			return TEICSSNAMEFILE;

		$cssName = 'obf/'.$this->_name;// unix only !
		$cssFileIn = CSS_FOLDER.$portal.DS.TEICSSNAMEFILE.'.css';
		$cssFileOut = CSS_FOLDER.$portal.DS.$cssName.'.css';
		$cssFileInPrint = CSS_FOLDER.$portal.DS.TEICSSNAMEFILE.'_print.css';
		$cssFileOutPrint = CSS_FOLDER.$portal.DS.$cssName.'_print.css';
		if(file_exists($cssFileOut)){
			// compare date files TEI and encodeTEI
			if( filemtime($cssFileIn)>filemtime($cssFileOut) || filemtime($cssFileInPrint)>filemtime($cssFileOutPrint) )
				$this->createCSS($cssFileIn,$cssFileOut,$cssFileInPrint,$cssFileOutPrint);
		} else {
			$this->createCSS($cssFileIn,$cssFileOut,$cssFileInPrint,$cssFileOutPrint);
		}
		return $cssName;
	}

	/**
	* Get encode elements
	*
	* @return string
	*/
	public function getELS(){
		if($_SESSION['level']==1)
			return $this->creatELS('tei');
		return file_get_contents($this->_shufflerDir.$this->_name.'.txt');
	}

	/**
	* Compil CSS
	*
	* @param String $fileIn File in
	* @param String $fileOut File out
	*/
	private function _compilCSS($fileIn,$fileOut){
		$CSS = file_get_contents($fileIn);
		$CSS = preg_replace_callback('#tei\\\:(\w+)([\s|\[]|:|\{|,)#si', array($this, 'replaceElementCSS'), $CSS);
		$CSS = preg_replace_callback('#\[(\w+)([=|\[])#si', array($this, 'replaceAttributeCSS'), $CSS);
		$CSS = preg_replace_callback('#attr\(\s*(\w+)\s*\)#si', array($this, 'replaceContentCSS'), $CSS);
		$CSS = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $CSS);
		$CSS = str_replace(array("\n", "\t", '  ', '   '), '', $CSS);
		file_put_contents($fileOut,$CSS);
	}

	/**
	* Create CSS TEI files
	*
	* @param String $cssFileIn CSS TEI file in
	* @param String $cssFileOut CSS TEI file out
	* @param String $cssFileInPrint CSS print TEI file in
	* @param String $cssFileOutPrint CSS print TEI file out
	*/
	protected function createCSS($cssFileIn,$cssFileOut,$cssFileInPrint,$cssFileOutPrint){
		$this->_compilCSS($cssFileIn,$cssFileOut);
		$this->_compilCSS($cssFileInPrint,$cssFileOutPrint);
	}

	/**
	* Convert elements in JS, JSON format, to Hex
	*
	* @param String $str Elements in JS, JSON format
	* @return String
	*/
	private function toHexELS($str){
		$str = substr($str,0,-3);
		$str = substr($str,11);
		return bin2hex($str);
	}

	/**
	* Creat relation elements in JS
	*
	* @param String $str Elements in JS, JSON format
	* @return String
	*/
	protected function creatELS($prefix){
		$els = array('nspace' => $prefix);
		if($_SESSION['level']==1){
			foreach ($this->_els as $tag => $attrs) {
				$els[$tag] = array('elm'=>$tag);
				foreach ($attrs as $att)
					$els[$tag][$att] = $att;
			}
		} else {
			foreach ($this->_els as $tag => $attrs) {
				$_tag = $this->_elements[$tag];
				$els[$tag] = array('elm'=>$_tag);
				foreach ($attrs as $att)
					$els[$tag][$att] = $this->_attributes[$att];
			}
		}
		return $this->toHexELS(json_encode($els));
	}

	/**
	* Replace TEI elements
	*
	* @param array $matches
	* @return String
	*/
	private function replaceElement($matches){
		return '<'.$matches[1].$this->_prefix.':'.$this->_elements[strtolower($matches[2])].$matches[3];
	}

	/**
	* Replace TEI attributes
	*
	* @param array $matches
	* @return String
	*/
	private function replaceAttribute($matches){
		if($matches[1]=='id')
			return ' id="';
		return ' '.$this->_attributes[$matches[1]].'="';
	}

	/**
	* Replace CSS elements
	*
	* @param array $matches
	* @return String
	*/
	private function replaceElementCSS($matches){
		return $this->_prefix.'\:'.$this->_elements[strtolower($matches[1])].$matches[2];
	}

	/**
	* Replace CSS attributes
	*
	* @param array $matches
	* @return String
	*/
	private function replaceAttributeCSS($matches){
		return '['.$this->_attributes[$matches[1]].$matches[2];
	}

	/**
	* Replace CSS content attributes
	*
	* @param array $matches
	* @return String
	*/
	private function replaceContentCSS($matches){
		return 'attr('.$this->_attributes[$matches[1]].')';
	}

	/**
	* Translate numeric to alphanumeric
	*
	* @param array $matches
	* @return String
	*/
	private function _num2alpha(){
		$n = $this->_pos;
		for($r = ""; $n >= 0; $n= intval($n / 26) - 1)
			$r = chr($n%26 + 0x41) . $r;

		if($r=='ID'){
			$this->_pos++;
			$r = $this->_num2alpha();
		}
		$this->_pos++;
		return strtolower($r);
	}
}
?>
