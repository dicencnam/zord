<?php
/**
* Cmd
* @package zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

class Cmd {

	/**
	* jing Command
	*
	* @param string $portal
	* @param string $book
	* @return string
	*/
	public static function jing($portal,$book) {
		if(Filter::validPortal($portal) && Filter::validEAN13($book)){
			$file = TEI_FOLDER.$portal.DS.$book.DS.$book.'.xml';
			$file_rng = TOOLS_FOLDER.'tei_all.rng';
			if(file_exists($file) && file_exists($file_rng)){
				return shell_exec('jing '.$file_rng.' '.$file);
			}
		}
		return 'Error check RNG file';
	}

	/**
	* epubcheck Command
	*
	* @param string $portal
	* @param string $book
	* @return string
	*/
	public static function epubcheck($portal,$book) {
		if(Filter::validPortal($portal) && Filter::validEAN13($book)){

				$epubcheck = TOOLS_FOLDER.'epubcheck'.DS.'epubcheck.jar';
				$epubFile = EPUBS_FOLDER.$portal.DS.$book.'.epub';
				$chekOutFile = EPUBS_FOLDER.$portal.DS.$book.'_check.txt';

				if(file_exists($epubcheck) && file_exists($epubFile)){
					if(file_exists($chekOutFile)){
						unlink($chekOutFile);
					}

					$cmd = 'java -jar '.$epubcheck.' '.$epubFile.' 2>&1 | tee '.$chekFile.' &';
					shell_exec($cmd);
				}
		}
		return 'Error ePubCheck';
	}
}

?>
