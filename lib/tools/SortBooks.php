<?php

/**
* SortBooks
* @package Zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class SortBooks {
	public function sortBycategCreationDate($books){
		foreach($books as $key => $bks){
			usort($bks, array($this,"cmp_creation_date_i"));
			$books[$key] = $bks;
		}
		return $books;
	}

	public function sortBycategDate($books){
		foreach($books as $key => $bks){
			usort($bks, array($this,"cmp_date_i"));
			$books[$key] = $bks;
		}
		return $books;
	}

	public function sortBycategNumber($books){
		foreach($books as $key => $bks){
			usort($bks, array($this,"cmp_category_number_i"));
			$books[$key] = $bks;
		}
		return $books;
	}

	public function sortCategNumber($books){
		usort($books, array($this,"cmp_category_number_i"));
		return $books;
	}

	public function sortCreationDate($books){
		usort($books, array($this,"cmp_creation_date_i"));
		return $books;
	}

	public function sortDate($books){
		usort($books, array($this,"cmp_date_i"));
		return $books;
	}

	public function cmp_creation_date_i($a, $b){
		$da = (int) $a['creation_date_i'];
		$db = (int) $b['creation_date_i'];
		if ($da == $db)
			return 0;
		return ($da < $db) ? -1 : 1;
	}

	public function cmp_date_i($a, $b){
		$da = (int) $a['date_i'];
		$db = (int) $b['date_i'];
		if ($da == $db)
			return 0;
		return ($da < $db) ? -1 : 1;
	}

	public function cmp_category_number_i($a, $b){
		$da = (int) $a['category_number_i'];
		$db = (int) $b['category_number_i'];
		if ($da == $db)
			return 0;
		return ($da < $db) ? -1 : 1;
	}
}
?>
