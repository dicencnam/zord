<?php
/**
* Meta Metadata
* @package zord
* @subpackage metadata
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Meta {

	/**
	* Content
	*
	* @var String
	*/
	protected $_dc = '';

	/**
	* Set data
	*
	* @param array $data
	*/
	public function set($data){
		foreach($data as $key => $value){
			if(method_exists($this, $key)){
				if($key=='title'){
					$subtitle = '';
					if(isset($data['subtitle']))
						$value = $value.', '.$data['subtitle'];
					$this->$key($value);
				} else {
					$this->$key($value);
				}
			}
		}
	}

	/**
	* Get data string
	*
	* @return string
	*/
	public function get(){
		return $this->_dc;
	}

	/**
	* Convert special characters to XML entities
	*
	* @param string $val
	* @return string
	*/
	public function xmlspecialchars($val) {
		return str_replace(array('&','>','<','"'), array('&#38;','&#62;','&#60;','&#34;'), $val);
	}
}
?>
