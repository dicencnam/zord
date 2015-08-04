<?php
/**
* Counter
* @package zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Counter {

	/**
	* Set request report 2
	*
	* @param String $userID User ID
	* @param String $isbn ISBN
	* @param String $part Part name of book
	*/
	public static function setReport_2($userID,$isbn,$part) {
		$file = COUNTER_FOLDER.$userID.'_2_'.date("Y-m").'.json';
		file_put_contents($file, $_SESSION['switcher']['name'].'|§|'.$isbn.'|§|'.$part."\n", FILE_APPEND);
	}

	/**
	* Set request report 5
	*
	* @param String $userID User ID
	* @param String $isbn ISBN
	*/
	public static function setReport_5($userID,$isbn) {
		$file = COUNTER_FOLDER.$userID.'_5_'.date("Y-m").'.json';
		file_put_contents($file, $_SESSION['switcher']['name'].'|§|'.$isbn."\n", FILE_APPEND);
	}

	/**
	* Get counter report 2
	*
	* @param String $userID User ID
	* @param String $start Start date yyyy-mm
	* @param String $end Part End date yyyy-mm
	* @return array
	*/
	public static function getReport_2($userID,$start,$end) {

		$start = explode('-',$start);
		$year = (int) $start[0];
		$month = (int) $start[1];
		$end = explode('-',$end);
		$endYear = (int) $end[0];
		$endMonth = (int) $end[1];

		$continu = true;
		$i = 0;
		$files = array();
		$stringFile = function($_userID,$_year,$_month){
			if($_month<10)
				$_month = '0'.$_month;
			return COUNTER_FOLDER.$_userID.'_2_'.$_year.'-'.$_month.'.json';
		};

		$stringDates = function($_year,$_month){
			if($_month<10)
				$_month = '0'.$_month;
			return $_year.'-'.$_month;
		};

		$dateArray = array('portal'=>array(),'tt'=>0,'dates'=>array());
		$datesString = array();
		$datesTT = array();
		$portals = array();
		$tt = 0;
		while($continu){
			$files[$year.'-'.$month] = $stringFile($userID,$year,$month);
			$dateArray['dates'][$year.'-'.$month] = 0;
			$datesTT[$year.'-'.$month] = 0;
			$datesString[] = $stringDates($year,$month);
			if($month!=12)
				$month++;
			if($month==12)
				$year++;
			if($year==$endYear && $month==$endMonth){
				$files[$year.'-'.$month] = $stringFile($userID,$year,$month);
				$dateArray['dates'][$year.'-'.$month] = 0;
				$datesTT[$year.'-'.$month] = 0;
				$datesString[] = $stringDates($year,$month);
				$continu = false;
			}
		}
		$documents = array();
		foreach($files as $date => $file){
			if(file_exists($file)){
				$content = explode("\n",trim(file_get_contents($file)));
				foreach($content as $line){
					$line = explode('|§|',$line);
					if(!isset($documents[$line[1]])){
						$documents[$line[1]] = $dateArray;
						$documents[$line[1]]['portal'][] = $line[0];
					}
					if(!in_array($line[0], $documents[$line[1]]['portal']))
						$documents[$line[1]]['portal'][] = $line[0];
					if(!in_array($line[0], $portals))
						$portals[] = $line[0];
					$documents[$line[1]]['dates'][$date]++;
					$documents[$line[1]]['tt']++;
					$datesTT[$date]++;
					$tt++;
				}
			}
		}
		return array('documents'=>$documents,'dates'=>$datesString, 'total'=>$tt,'datesTT'=>$datesTT,'portals'=>$portals);
	}

	public static function getReport_5($userID,$start,$end) {

		$start = explode('-',$start);
		$year = (int) $start[0];
		$month = (int) $start[1];
		$end = explode('-',$end);
		$endYear = (int) $end[0];
		$endMonth = (int) $end[1];

		$continu = true;
		$i = 0;
		$files = array();
		$stringFile = function($_userID,$_year,$_month){
			if($_month<10)
				$_month = '0'.$_month;
			return COUNTER_FOLDER.$_userID.'_5_'.$_year.'-'.$_month.'.json';
		};

		$stringDates = function($_year,$_month){
			if($_month<10)
				$_month = '0'.$_month;
			return $_year.'-'.$_month;
		};

		$dateArray = array('portal'=>array(),'tt'=>0,'dates'=>array());
		$datesString = array();
		$datesTT = array();
		$portals = array();
		$tt = 0;
		while($continu){
			$files[$year.'-'.$month] = $stringFile($userID,$year,$month);
			$dateArray['dates'][$year.'-'.$month] = 0;
			$datesTT[$year.'-'.$month] = 0;
			$datesString[] = $stringDates($year,$month);
			if($month!=12)
				$month++;
			if($month==12)
				$year++;
			if($year==$endYear && $month==$endMonth){
				$files[$year.'-'.$month] = $stringFile($userID,$year,$month);
				$dateArray['dates'][$year.'-'.$month] = 0;
				$datesTT[$year.'-'.$month] = 0;
				$datesString[] = $stringDates($year,$month);
				$continu = false;
			}
		}
		$documents = array();
		foreach($files as $date => $file){
			if(file_exists($file)){
				$content = explode("\n",trim(file_get_contents($file)));
				foreach($content as $line){
					$line = explode('|§|',$line);
					if(!isset($documents[$line[1]])){
						$documents[$line[1]] = $dateArray;
						$documents[$line[1]]['portal'][] = $line[0];
					}
					if(!in_array($line[0], $documents[$line[1]]['portal']))
						$documents[$line[1]]['portal'][] = $line[0];
					if(!in_array($line[0], $portals))
						$portals[] = $line[0];
					$documents[$line[1]]['dates'][$date]++;
					$documents[$line[1]]['tt']++;
					$datesTT[$date]++;
					$tt++;
				}
			}
		}
		return array('documents'=>$documents,'dates'=>$datesString, 'total'=>$tt,'datesTT'=>$datesTT,'portals'=>$portals);
	}
}
?>
