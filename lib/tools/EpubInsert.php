<?php
/**
* EpubInsert
* @package zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

class EpubInsert {
	public $init = false;
	private $_opfFile = '';
	private $_coverID = -1;
	private $_identifier = '';
	private $_metadata = array();
	private $_spine = array();
	private $_manifest = array();
	private $_guide = array();
	private $_incID = 0;
	private $_ncxID = 'ncx';
	private $_ncxFilesDel = array();
	private $_zip = null;
	private $_dc = null;

	private $_elementName = array(
		'manifest' => 'item',
		'spine' => 'itemref',
		'guide' => 'reference',
	);

	private $_type_mime = array(
		'xhtml' => 'application/xhtml+xml',
		'html' => 'application/xhtml+xml',
		'png' => 'image/png',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'ttf' => 'application/x-font-ttf',
		'otf' => 'application/x-font-otf',
		'css' => 'text/css',
		'svg' => 'image/svg+xml',
		'ncx' => 'application/x-dtbncx+xml'
	);

	public function __construct($zip,$metadata=array()){
		$this->_zip = new ZipArchive;
		$res = $this->_zip->open($zip);
		if ($res === TRUE) {
			$containerXML = simplexml_load_string($this->_zip->getFromName('META-INF/container.xml'));
			if($containerXML && $containerXML->rootfiles->rootfile['full-path'] != null){
				$this->_opfFile = $containerXML->rootfiles->rootfile['full-path'].'';
				$this->_opfDir = dirname($this->_opfFile).'/';
				$opf = $this->_zip->getFromName($containerXML->rootfiles->rootfile['full-path'].'');
				if($opf){
					preg_match('#<spine\s+toc="(.*?)"#ms', $opf, $toc);
					$this->_ncxID = $toc[1];
					preg_match('#<spine\s+toc="'.$this->_ncxID.'">(.*?)</spine>#ms', $opf, $spine);
					$this->_spine = $this->_getLinesAttributes($spine[1],'itemref');
					preg_match('#<manifest>(.*?)</manifest>#ms', $opf, $manifest);
					$this->_manifest = $this->_getLinesAttributes($manifest[1],'item');
					preg_match('#<guide>(.*?)</guide>#ms', $opf, $guide);
					$this->_guide = $this->_getLinesAttributes($guide[1],'reference');
					$this->_getCoverId($opf);
					$this->addMetadataIdentifier('URN',$this->_genUUID(),true);
					$this->addMetadata('date',date("Y-m"),array(
						'opf:event'=>'epub-publication',
						'xsi:type'=>'dcterms:W3CDTF'
					));
					$this->init = true;
					$this->_dc = new DC_epub();
					$this->_dc->set($metadata);
				}
			}
		}
	}

	protected function _getCoverId($opf){
		$meta = $this->_getLinesAttributes($opf,'meta ');
		foreach ($meta as $attrs) {
			if(isset($attrs['name']) && $attrs['name']=='cover'){
				$coverID = $attrs['content'];
				foreach ($this->_manifest as $key => $value) {
					if(isset($value['id']) && isset($value['href']) && $value['id']==$coverID){
						$this->_coverID = $key;
					}
				}
			}
		}
	}

	protected function _updateNCX(){
		$ncx = null;
		$_countP = 0;
		foreach ($this->_manifest as $Mkey => $value) {
			if(isset($value['id']) && isset($value['href']) && $value['id']==$this->_ncxID && $value['media-type']=='application/x-dtbncx+xml'){
				$ncxFile = $this->_opfDir.$value['href'];
				$ncxContent = $this->_zip->getFromName($ncxFile);
				$ncx = simplexml_load_string($ncxContent);
				break;
			}
		}

		if($ncx){
			// update uid
			foreach ($ncx->head->meta as $key => $meta) {
				if ($meta['name'].''=='dtb:uid') {
					$meta['content'] = $this->_identifier;
				}
			}
			// del files 1 level
			if(count($this->_ncxFilesDel)>0){
				foreach ($ncx->navMap->navPoint as $navPointKey => $navPointP) {
					if (in_array($navPointP->content['src'].'', $this->_ncxFilesDel)) {
						unset($ncx->navMap->navPoint[$_countP]);
					}
					$_countP++;
				}
			}
			// save
			$this->_zip->deleteName($ncxFile);
			$this->_zip->addFromString($ncxFile, $ncx->asXML());
		}
	}

	protected function _getLinesAttributes($string,$elName){
		$ar = array();
		preg_match_all('#<'.$elName.'(.*?)/>#ms', $string, $attributes);
		foreach ($attributes[0] as $attrs) {
			preg_match_all('#([\w|-]+)="(.*?)"#ms', $attrs, $attValue);
			$_attr = array();
			foreach ($attValue[1] as $key => $att)
				$_attr[$att] = $attValue[2][$key];
			$ar[] = $_attr;
		}
		return $ar;
	}

	public function addManifest($fileName,$id=''){
		$extension = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
		if($id==''){
			$this->_incID++;
			$id = 'zordm-'.$this->_incID;
		}
		$this->_manifest[] = array('id'=>$id,'href'=>$fileName,'media-type'=>$this->_type_mime[$extension]);
	}


	public function addMetadata($name,$value,$attributes=array()) {
		$this->_metadata[] = array('key'=>$name,'value'=>$value,'attributes'=>$attributes);
	}

	public function addSource($source,$target){
		$this->_zip->addFile($source, $this->_opfDir.$target);
		$this->addManifest($target);
	}
	/**
	*
	*
	* @param
	*/
	public function addXHTML($content,$options){

		$this->_incID++;
		$id = 'zordp-'.$this->_incID;
		$fileName = $id.'.html';
		$this->addManifest($fileName,$id);
		if(isset($options['spine']))
			$this->addSpine($id,$options['spine']['position'],$options['spine']['linear']);

		if(isset($options['guide']))
			$this->addGuide($fileName,$options['guide']['position'],$options['guide']['title'],$options['guide']['type']);

		$this->_zip->addFromString( $this->_opfDir.$fileName,$content);
	}

	/**
	*
	*
	* @param
	*/
	public function addGuide($href,$position=null,$title='',$type='text'){
		if($position===null){
			$this->_guide[] = array('href'=>$href,'title'=>$title,'type'=>$type);
		} else {
			array_splice( $this->_guide, $position, 0, array(array('href'=>$href,'title'=>$title,'type'=>$type)) );
		}
	}

	/**
	*
	*
	* @param
	*/
	public function addSpine($idref,$position=null,$linear='yes'){
		if($position===null){
			$this->_spine[] = array('idref'=>$idref,'linear'=>$linear);
		} else {
			array_splice( $this->_spine, $position, 0, array(array('idref'=>$idref,'linear'=>$linear)) );
		}
	}

	protected function _setLinesAttributes($name){
		$lines = array();

		$nameVar = '_'.$name;
		foreach ($this->$nameVar as $attributes) {
			$attr = '';
			ksort($attributes);
			foreach ($attributes as $key => $value)
				$attr .= ' '.$key.'="'.$value.'"';
			$lines[] = "\t\t<".$this->_elementName[$name].$attr."/>";
		}
		$stringLines = implode("\n",$lines)."\n";

		if($name=='spine')
			return "\t<spine toc=\"".$this->_ncxID."\">\n".$stringLines."\t</spine>\n";
		else
			return "\t<".$name.">\n".$stringLines."\t</".$name.">\n";
	}

	private function _xmlspecialchars($val) {
		return str_replace(array('&','>','<','"'), array('&#38;','&#62;','&#60;','&#34;'), $val);
	}

	private function _metadataXML(){
		$lines = array();
		foreach ($this->_metadata as $attributes) {
			$attr = '';
			foreach ($attributes['attributes'] as $key => $value)
				$attr .= ' '.$key.'="'.$value.'"';
			$lines[] = "\t\t<dc:".$attributes['key'].$attr.'>'.$this->_dc->xmlspecialchars($attributes['value']).'</dc:'.$attributes['key'].">";
		}
		$metaCover = '';
		if($this->_coverID>-1){
			$metaCover = "\t\t<meta name=\"cover\" content=\"".$this->_manifest[$this->_coverID]['id']."\"/>\n";
		}
		return "\t".'<metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\n".implode("\n",$lines).$this->_dc->get().$metaCover."\t</metadata>\n";
	}

	private function _genUUID() {
		$md5 = md5(uniqid('', true));
		return substr($md5, 0, 8 ).
				'-'.substr($md5, 8, 4).
				'-'.substr($md5, 12, 4).
				'-'.substr($md5, 16, 4).
				'-'.substr($md5, 20, 12);
	}

	public function addMetadataIdentifier($key,$value,$uid=false){
		$attributes = array(
			'opf:scheme' => $key
		);
		if($uid)
			$attributes['id'] = 'uid';

		if($key=='URN'){
			$_value = 'urn:uuid:'.$value;
			$this->_identifier = $_value;
		} else {
			$_value = $value;
		}

		$this->_metadata[] = array(
			'key'=>'identifier',
			'value'=>$_value,
			'attributes'=>$attributes
			);
	}

	public function updateCover($file){
		if($this->_coverID>-1){
			$href = $this->_manifest[$this->_coverID]['href'];
			$extension = strtolower(pathinfo($file,PATHINFO_EXTENSION));
			$this->_manifest[$this->_coverID]['media-type'] = $this->_type_mime[$extension];
			$this->_zip->deleteName($this->_opfDir.$href);
			$href = substr_replace($href , $extension, strrpos($href , '.') +1);
			$this->_zip->addFile($file, $this->_opfDir.$href);
			$this->_manifest[$this->_coverID]['href'] = $href;
		}
	}

	public function removeFile($file){
		foreach ($this->_manifest as $manifestKey => $manifestValue) {
			if($manifestValue['href']==$file){
				foreach ($this->_guide as $guideKey => $guideValue) {
					if($manifestValue['href']==$guideValue['href']){
						unset($this->_guide[$guideKey]);
						break;
					}
				}
				foreach ($this->_spine as $spineKey => $spineValue) {
					if($manifestValue['id']==$spineValue['idref']){
						unset($this->_spine[$spineKey]);
						break;
					}
				}
				$this->_ncxFilesDel[] = $manifestValue['href'];
				$this->_zip->deleteName($this->_opfDir.$manifestValue['href']);
				unset($this->_manifest[$manifestKey]);
				break;
			}
		}
	}

	public function getSpineCount(){
		return count($this->_spine);
	}

	public function getGuideCount(){
		return count($this->_guide);
	}

	public function close(){
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<package version="2.0" unique-identifier="uid" xmlns="http://www.idpf.org/2007/opf">'."\n";
		// metadata
		 $xml .= $this->_metadataXML();
		// manifest
		$xml .= $this->_setLinesAttributes('manifest');
		// spine
		$xml .= $this->_setLinesAttributes('spine');
		// guide
		$xml .= $this->_setLinesAttributes('guide').'</package>';

		// @$this->_updateNCX();// warning ??
echo $xml;
		// $this->_zip->deleteName($this->_opfFile);
		// $this->_zip->addFromString($this->_opfFile, $xml);

		$this->_zip->close();
	}
}

?>
