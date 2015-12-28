<?php
/**
* EpubI
* @package zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

class Epub  extends ZipArchive {

	private $_opfDir = 'OPS/';
	private $_coverID = 'cover-id';
	private $_identifier = '';
	private $_title = '';
	private $_metadata = array();
	private $_spine = array();
	private $_manifest = array();
	private $_guide = array();
	private $_incID = 0;
	private $_ncxID = 'ncx';
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

	private function _OPS($f) {
		return $this->_opfDir.$f;
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

	public function addMetadata($name,$value,$attributes=array()) {
		$this->_metadata[] = array('key'=>$name,'value'=>$value,'attributes'=>$attributes);
	}

	public function addManifest($fileName,$id=''){
		$extension = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
		if($id==''){
			$this->_incID++;
			$id = 'zordm-'.$this->_incID;
		}
		$this->_manifest[] = array('id'=>$id,'href'=>$fileName,'media-type'=>$this->_type_mime[$extension]);
	}

	public function addSource($source,$target){
		$this->addFile($source, $this->_opfDir.$target);
		$this->addManifest($target);
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

	public function addCover($coverIMGFile,$extension) {
		$coverHTMLFile_epub = 'cover.xhtml';
		$coverIMGFile_epub = 'medias/cover.'.$extension;

		$this->_manifest[$this->_coverID] = array();

		// html --------------------------------------------
		$html = Tpl::render('admin/fileXHTML11',array(
				'name' => 'Cover',
				'css' => '',
				'html' => '<div style="text-align: center; page-break-after: always;"><img src="'.$coverIMGFile_epub.'" alt="Cover" style="height: 100%; max-width: 100%;"/></div>',
			)
		);
		$this->addXHTML(
			$html,
			'cover',
			array(
				'spine' 	=> array(
					'position'	=> null,
					'linear'		=> 'yes'
				),
				'guide' 	=> array(
					'position'	=> null,
					'title'			=> 'Cover',
					'type'			=> 'text'
				)
			)
		);

		// image --------------------------------------------
		$this->_manifest[$this->_coverID]['id'] = 'cover-image';
		$this->_manifest[$this->_coverID]['href'] = $coverIMGFile_epub;
		$this->_manifest[$this->_coverID]['media-type'] = $this->_type_mime[$extension];
		$this->addFile($coverIMGFile, $this->_opfDir.$coverIMGFile_epub);
	}

	public function addNCX($content) {
		$ncxFile = 'navigation.ncx';

$ncxString = '<?xml version="1.0"?>
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">
<head>
<meta name="dtb:uid" content="'.$this->_identifier.'"/>
<meta name="dtb:totalPageCount" content="0"/>
<meta name="dtb:maxPageNumber" content="0"/>
</head>
<docTitle>
<text>'.$this->_title.'</text>
</docTitle>
'.$content.'
</ncx>';
		$this->addManifest($ncxFile,$this->_ncxID);
		$this->addFromString($this->_opfDir.$ncxFile, $ncxString);
	}

	/**
	*
	*
	* @param
	*/
	public function addXHTML($content,$fileName,$options){
		$this->_incID++;
		$id = 'zordp-'.$this->_incID;
		$fileName = $fileName.'.xhtml';
		$this->addManifest($fileName,$id);
		if(isset($options['spine']))
			$this->addSpine($id,$options['spine']['position'],$options['spine']['linear']);

		if(isset($options['guide']))
			$this->addGuide($fileName,$options['guide']['position'],$options['guide']['title'],$options['guide']['type']);

		$this->addFromString( $this->_opfDir.$fileName,$content);
	}

	private function _metadataXML(){
		$lines = array();
		foreach ($this->_metadata as $attributes) {
			$attr = '';
			foreach ($attributes['attributes'] as $key => $value)
				$attr .= ' '.$key.'="'.$value.'"';
			$lines[] = "\t\t<dc:".$attributes['key'].$attr.'>'.$this->_dc->xmlspecialchars($attributes['value']).'</dc:'.$attributes['key'].">";
		}
		$metaCover = "\t\t<meta name=\"cover\" content=\"".$this->_manifest[$this->_coverID]['id']."\"/>\n";
		return "\t".'<metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\n".implode("\n",$lines).$this->_dc->get().$metaCover."\t</metadata>\n";
	}

	private function _setLinesAttributes($name){
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

	public function __construct($filEepub,$metadata=array()){
		file_put_contents($filEepub, base64_decode("UEsDBAoAAAAAAOmRAT1vYassFAAAABQAAAAIAAAAbWltZXR5cGVhcHBsaWNhdGlvbi9lcHViK3ppcFBLAQIUAAoAAAAAAOmRAT1vYassFAAAABQAAAAIAAAAAAAAAAAAIAAAAAAAAABtaW1ldHlwZVBLBQYAAAAAAQABADYAAAA6AAAAAAA="));
		parent::open($filEepub);
		$this->addFromString('META-INF/container.xml', '<?xml version="1.0" encoding="UTF-8" ?>
	<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container"><rootfiles><rootfile full-path="OPS/package.opf" media-type="application/oebps-package+xml"/></rootfiles></container>');

		$this->addMetadataIdentifier('URN',$this->_genUUID(),true);
		$this->addMetadata('date',date("Y-m"),array(
			'opf:event'=>'epub-publication',
			'xsi:type'=>'dcterms:W3CDTF'
		));
		$this->_dc = new DC_epub();
		$this->_dc->set($metadata);

		$this->_title = $metadata['title'];
		if(isset($metadata['subtitle']))
			$this->_title .= '. '.$metadata['subtitle'];

	}

	public function getSpineCount(){
		return count($this->_spine);
	}

	public function getGuideCount(){
		return count($this->_guide);
	}

	public function finish(){
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<package version="2.0" unique-identifier="uid" xmlns="http://www.idpf.org/2007/opf">'."\n";
		// metadata
		 $xml .= $this->_metadataXML();
		// manifest
		$xml .= $this->_setLinesAttributes('manifest');
		// // spine
		$xml .= $this->_setLinesAttributes('spine');
		// // guide
		$xml .= $this->_setLinesAttributes('guide').'</package>';
		//
		$this->addFromString($this->_opfDir.'package.opf', $xml);
		$this->close();
	}

}
?>
