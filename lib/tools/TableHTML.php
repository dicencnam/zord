<?php

/**
* TableHTML
* @package zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class TableHTML {

	/**
	* Template cells list
	*
	* @var array
	*/
	public $tpl = array();

	/**
	* Check line option
	*
	* @var array
	*/
	public $checkline = false;

	/**
	* HTML array
	*
	* @var array
	*/
	protected $html = array();

	/**
	* Date model list
	*
	* @var array
	*/
	protected $dates = array('creation_date_i','creation_date_after_i','date_i');

	/**
	* Persons model list
	*
	* @var array
	*/
	protected $persons = array('creator_ss','editor_ss');


	/**
	* Constructor
	*
	* @param array $model Template cells list
	*/
	public function __construct($model){
		$this->tpl = $model['tpl'];
		if(isset($model['checkline']))
			$this->checkline = $model['checkline'];
	}

	/**
	* Set new row
	*
	* @param array $data Cells data
	*/
	public function set($data){
		$line = array();
		foreach($this->tpl as $cell){
			if(in_array($cell, $this->dates)){
				$line[] = $this->_getDate($cell,$data);
			} else if(in_array($cell, $this->persons)){
				$line[] = $this->_getPersons($cell,$data);
			} else if($cell=='book_s'){
				$line[] = $this->_getID($cell,$data);
			} else if($cell=='title'){
				$line[] = $this->_getTitle($data);
			} else {
				$line[] = $this->_getCell($cell,$data);
			}
		}
		$this->_setLine($line,$data['level_i']);
	}

	/**
	* Get table HTML
	*
	* @return string
	*/
	public function get(){
		return implode('',$this->html);
	}

	/**
	* Set line
	*
	* @param array $line
	* @param integer $level Level publication
	*/
	protected function _setLine($line,$level){
		$cl = '';
		if($level>0)
			$cl = ' class="draft"';
		$this->html[] = '<tr'.$cl.'>'.implode('',$line).'</tr>';
	}

	/**
	* Get id
	*
	* @param strinf $cell Cell name
	* @param array $data
	* @return string HTML
	*/
	protected function _getID($cell,$data){
		$persons = '';
		if($this->checkline)
			return '<td class="t_check"><input type="checkbox" value="'.$data[$cell].'" data-type="check"></td><td class="t_id">'.$data[$cell].'</td>';
		return '<td class="t_id">'.$data[$cell].'</td>';
	}

	/**
	* Get persons
	*
	* @param strinf $cell Cell name
	* @param array $data
	* @return string HTML
	*/
	protected function _getPersons($cell,$data){
		$persons = '';
		$data_cell = '';
		if(isset($data[$cell])){
			$etAl = false;
			$_p = $data[$cell];
			if(count($_p)>3){
				$_p = array_slice($_p, 0, 3);
				$etAl = true;
			}

			$persons = implode('&#160;; ',$_p);
			if($etAl)
				$persons .= ', <i>et al.</i>';
			$data_cell = implode('|',$data[$cell]);
		}
		return '<td class="t_person" data-'.$cell.'="'.$data_cell.'">'.$persons.'</td>';
	}

	/**
	* Get title
	*
	* @param array $data
	* @return string HTML
	*/
	protected function _getTitle($data){
		$subtitle = '';
		if(isset($data['subtitle_s']))
			$subtitle = '. '.$data['subtitle_s'];
		$title = '';
		if(isset($data['title_s']))
			$title = $data['title_s'];

		$t = $title.$subtitle;
		return '<td class="t_title" data-sort="'.$this->_clearTitle($t).'"><a href="'.BASEURL.$data['book_s'].'">'.$t.'</a></td>';
	}

	protected function _clearTitle($val){
		return str_replace(array('>','<','"'), '', $val);
	}

	/**
	* Get date
	*
	* @param strinf $cell Cell name
	* @param array $data
	* @return string HTML
	*/
	protected function _getDate($cell,$data){
		$date = '';
		if(isset($data[$cell]))
			$date = $data[$cell];
		return '<td class="t_date">'.$date.'</td>';
	}

	/**
	* Get cell
	*
	* @param strinf $cell Cell name
	* @param array $data
	* @return string HTML
	*/
	protected function _getCell($cell,$data){
		return '<td>'.$data[$cell].'</td>';
	}
}
?>
