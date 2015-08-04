<?php
/**
* Response
* @package Micro
* @subpackage Response
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
abstract Class Response {

	/**
	* Hearders
	*
	* @var Array
	*/
	protected $_headers = array();

	/**
	* Status code
	*
	* @var String
	*/
	protected $_statusCode = '200';

	/**
	* Reason Phrase
	*
	* @var String
	*/
	protected $_reasonPhrase = 'OK';

	/**
	* Out
	*
	*/
	abstract public function printOut();

	/**
	* Error
	*
	*/
	abstract public function error();

	/**
	* Send hearders
	*
	*/
	protected function sendHeaders(){
		header("HTTP/1.0 ".$this->_statusCode.' '.$this->_reasonPhrase);
		foreach($this->_headers as $k => $v)
			header($k.': '.$v);
	}

	/**
	* Set hearder
	*
	* @param String $key
	* @param String $value
	*/
	public function setHeader($key,$value) {
		$this->_headers[$key] = $value;
	}

	/**
	* Set hearder
	*
	* @param Integer $code
	* @return Array
	*/
	public function statusCode($code) {
		switch ($code) {
			// Informational
			case 100: $s = 'Continue'; break;
			case 101: $s = 'Switching Protocols'; break;
			case 102: $s = 'Processing'; break;
			case 118: $s = 'Connection timed out'; break;

			// Success
			case 200: $s = 'OK'; break;
			case 201: $s = 'Created'; break;
			case 202: $s = 'Accepted'; break;
			case 203: $s = 'Non-Authoritative Information'; break;
			case 204: $s = 'No Content'; break;
			case 205: $s = 'Reset Content'; break;
			case 206: $s = 'Partial Content'; break;
			case 207: $s = 'Multi-Status'; break;
			case 210: $s = 'Content Different'; break;
			case 226: $s = 'IM Used'; break;

			// Redirection
			case 300: $s = 'Multiple Choices'; break;
			case 301: $s = 'Moved Permanently'; break;
			case 302: $s = 'Moved Temporarily'; break;
			case 303: $s = 'See Other'; break;
			case 304: $s = 'Not Modified'; break;
			case 305: $s = 'Use Proxy'; break;
			case 306: $s = 'Switch Proxy'; break;
			case 307: $s = 'Temporary Redirect'; break;
			case 308: $s = 'Permanent Redirect'; break;
			case 310: $s = 'Too many Redirects'; break;

			// Client Error
			case 400: $s = 'Bad Request'; break;
			case 401: $s = 'Unauthorized'; break;
			case 402: $s = 'Payment Required'; break;
			case 403: $s = 'Forbidden'; break;
			case 404: $s = 'Not Found'; break;
			case 405: $s = 'Method Not Allowed'; break;
			case 406: $s = 'Not Acceptable'; break;
			case 407: $s = 'Proxy Authentication Required'; break;
			case 408: $s = 'Request Time-out'; break;
			case 409: $s = 'Conflict'; break;
			case 410: $s = 'Gone'; break;
			case 411: $s = 'Length Required'; break;
			case 412: $s = 'Precondition Failed'; break;
			case 413: $s = 'Request Entity Too Large'; break;
			case 414: $s = 'Request-URI Too Large'; break;
			case 415: $s = 'Unsupported Media Type'; break;
			case 416: $s = 'Requested range unsatisfiable'; break;
			case 417: $s = 'Expectation failed'; break;
			case 418: $s = 'I’m a teapot'; break;
			case 422: $s = 'Unprocessable entity'; break;
			case 423: $s = 'Locked'; break;
			case 424: $s = 'Method failure'; break;
			case 425: $s = 'Unordered Collection'; break;
			case 426: $s = 'Upgrade Required'; break;
			case 428: $s = 'Precondition Required'; break;
			case 429: $s = 'Too Many Requests'; break;
			case 431: $s = 'Request Header Fields Too Large'; break;
			case 449: $s = 'Retry With'; break;
			case 450: $s = 'Blocked by Windows Parental Controls'; break;
			case 456: $s = 'Unrecoverable Error'; break;
			case 499: $s = 'client has closed connection'; break;

			// Server Error
			case 500: $s = 'Internal Server Error'; break;
			case 501: $s = 'Not Implemented'; break;
			case 502: $s = 'Bad Gateway'; break;
			case 503: $s = 'Service Unavailable'; break;
			case 504: $s = 'Gateway Time-out'; break;
			case 505: $s = 'HTTP Version not supported'; break;
			case 506: $s = 'Variant also negociate'; break;
			case 507: $s = 'Insufficient storage'; break;
			case 508: $s = 'Loop detected'; break;
			case 509: $s = 'Bandwidth Limit Exceeded'; break;
			case 510: $s = 'Not extended'; break;
			default :
				$code = 520;
				$s = 'Web server is returning an unknown error';
			break;
		}
		$this->_statusCode = $code;
		$this->_reasonPhrase = $s;
		return array(
			'code' => $code,
			'reason' => $s
		);
	}
}
?>
