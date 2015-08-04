<?php
/**
* Binary response
* @package Micro
* @subpackage Response
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
Class Response_BINARY extends Response {

	/**
	* Response type
	*
	* @var String
	*/
	public $type = 'BINARY';

	/**
	* Out content
	*
	* @var String
	*/
	public $content = '';

	/**
	* Mime type
	*
	* @var Array
	*/
	private $_mime_types = array(
		'afm' => 'application/x-font-type1',
		'ai' => 'application/postscript',
		'air' => 'application/vnd.adobe.air-application-installer-package+zip',
		'atom' => 'application/atom+xml',
		'au' => 'audio/basic',
		'avi' => 'video/x-msvideo',
		'bdf' => 'application/x-font-bdf',
		'bmp' => 'image/bmp',
		'css' => 'text/css',
		'csv' => 'text/csv',
		'doc' => 'application/msword',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'eps' => 'application/postscript',
		'epub' => 'application/epub+zip',
		'gif' => 'image/gif',
		'htm' => 'text/html',
		'html' => 'text/html',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'jpgm' => 'video/jpm',
		'jpgv' => 'video/jpeg',
		'jpm' => 'video/jpm',
		'js' => 'application/javascript',
		'json' => 'application/json',
		'jsonml' => 'application/jsonml+json',
		'm2a' => 'audio/mpeg',
		'm2v' => 'video/mpeg',
		'm3a' => 'audio/mpeg',
		'm3u' => 'audio/x-mpegurl',
		'm4u' => 'video/vnd.mpegurl',
		'm4v' => 'video/x-m4v',
		'mathml' => 'application/mathml+xml',
		'mid' => 'audio/midi',
		'midi' => 'audio/midi',
		'mods' => 'application/mods+xml',
		'mov' => 'video/quicktime',
		'mp2' => 'audio/mpeg',
		'mp2a' => 'audio/mpeg',
		'mp3' => 'audio/mpeg',
		'mp4' => 'video/mp4',
		'mp4a' => 'audio/mp4',
		'mp4v' => 'video/mp4',
		'mpe' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpg4' => 'video/mp4',
		'mpga' => 'audio/mpeg',
		'mrc' => 'application/marc',
		'mrcx' => 'application/marcxml+xml',
		'ncx' => 'application/x-dtbncx+xml',
		'odb' => 'application/vnd.oasis.opendocument.database',
		'odc' => 'application/vnd.oasis.opendocument.chart',
		'odf' => 'application/vnd.oasis.opendocument.formula',
		'odft' => 'application/vnd.oasis.opendocument.formula-template',
		'odg' => 'application/vnd.oasis.opendocument.graphics',
		'odi' => 'application/vnd.oasis.opendocument.image',
		'odm' => 'application/vnd.oasis.opendocument.text-master',
		'odp' => 'application/vnd.oasis.opendocument.presentation',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		'odt' => 'application/vnd.oasis.opendocument.text',
		'oga' => 'audio/ogg',
		'ogg' => 'audio/ogg',
		'ogv' => 'video/ogg',
		'ogx' => 'application/ogg',
		'opf' => 'application/oebps-package+xml',
		'otf' => 'application/x-font-otf',
		'otg' => 'application/vnd.oasis.opendocument.graphics-template',
		'oth' => 'application/vnd.oasis.opendocument.text-web',
		'oti' => 'application/vnd.oasis.opendocument.image-template',
		'otp' => 'application/vnd.oasis.opendocument.presentation-template',
		'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
		'ott' => 'application/vnd.oasis.opendocument.text-template',
		'pdf' => 'application/pdf',
		'pfa' => 'application/x-font-type1',
		'pfb' => 'application/x-font-type1',
		'pfm' => 'application/x-font-type1',
		'pfr' => 'application/font-tdpfr',
		'pic' => 'image/x-pict',
		'png' => 'image/png',
		'ppt' => 'application/vnd.ms-powerpoint',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'ps' => 'application/postscript',
		'psd' => 'image/vnd.adobe.photoshop',
		'psf' => 'application/x-font-linux-psf',
		'qt' => 'video/quicktime',
		'rar' => 'application/x-rar-compressed',
		'rdf' => 'application/rdf+xml',
		'rss' => 'application/rss+xml',
		'rtf' => 'application/rtf',
		'sgm' => 'text/sgml',
		'sgml' => 'text/sgml',
		'snd' => 'audio/basic',
		'snf' => 'application/x-font-snf',
		'srx' => 'application/sparql-results+xml',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		'swa' => 'application/x-director',
		'swf' => 'application/x-shockwave-flash',
		'tar' => 'application/x-tar',
		'tei' => 'application/tei+xml',
		'text' => 'text/plain',
		'tif' => 'image/tiff',
		'tiff' => 'image/tiff',
		'ttc' => 'application/x-font-ttf',
		'ttf' => 'application/x-font-ttf',
		'txt' => 'text/plain',
		'vcard' => 'text/vcard',
		'vcf' => 'text/x-vcard',
		'wav' => 'audio/x-wav',
		'weba' => 'audio/webm',
		'webm' => 'video/webm',
		'webp' => 'image/webp',
		'wmv' => 'application/octet-stream',
		'woff' => 'application/x-font-woff',
		'xaml' => 'application/xaml+xml',
		'xap' => 'application/x-silverlight-app',
		'xhtml' => 'application/xhtml+xml',
		'xls' => 'application/vnd.ms-excel',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'xml' => 'application/xml',
		'xsl' => 'application/xml',
		'xslt' => 'application/xslt+xml',
		'zip' => 'application/zip'
	);

	private function _setContentType($ext){
		if( isset( $this->_mime_types[$ext] ) ){
			$ctype = $this->_mime_types[$ext];
			$this->_headers['Content-Type'] = $ctype;
		} else {
			$this->_headers['Content-Type'] = 'application/octet-stream';
			$this->_headers['Content-Transfer-Encoding'] = 'Binary';
		}
	}
	/**
	* Out
	*/
	public function printOut(){
		if(is_array($this->content)){
			$this->_setContentType($this->content['extension']);
			if(isset($this->content['filename']))
				$this->_headers['Content-Disposition'] = 'attachment; filename="'.$this->content['filename'].'"';

			$this->sendHeaders();
			echo $this->content['content'];
		} else if(file_exists($this->content)){
			// Determine Content Type
			$ext = strtolower(pathinfo($this->content,PATHINFO_EXTENSION));

			$this->_setContentType($ext);

			// file name
			//$this->_headers['Content-Disposition'] = 'attachment; filename="'.basename($this->content).'"';

			// File size
			$fsize = filesize($this->content);
			$this->_headers['Content-Length'] = $fsize;

			$this->sendHeaders();
			readfile($this->content);
		} else {
			$this->error();
		}
	}

	/**
	* Error
	*/
	public function error(){
		$this->_headers['Content-Type'] = 'text/html;charset=UTF-8';
		$this->_headers['Vary'] = 'Accept';
		$codeReason = $this->statusCode(404);
		$this->sendHeaders();
		echo Tpl::render('error/page',$codeReason);
	}
}
?>
