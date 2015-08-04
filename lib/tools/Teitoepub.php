<?php
/**
* Teitoepub
* @package zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Teitoepub {

	/**
	* Create ePub
	*
	* @param string $fileName TEI XML file name
	* @param string $portal Portal name
	* @return array Errors messages
	*/
	public function create($fileName,$portal){

		$file = TEI_FOLDER.$portal.DS.$fileName.DS.$fileName.'.xml';
		$error = false;
		$errorMsg = '';

		if(file_exists($file)){
			include(PROFILES_FOLDER.$portal.DS.'epub'.DS.'def.php');
			// portal folder
			if(!file_exists(EPUBS_FOLDER.$portal))
				mkdir(EPUBS_FOLDER.$portal,0777);

			if(!file_exists(TEMP_FOLDERl))
				mkdir(TEMP_FOLDER,0777);

			// copy source and image in new floder
			// create temp folder
			$tempFolder = TEMP_FOLDER.uniqid();
			mkdir($tempFolder,0777);
			// copy source
			$newfile = $tempFolder.DS.$fileName.'.xml';
			$contentXML = file_get_contents($file);
			// del facs attribute
			$contentXML = preg_replace('#\sfacs=".*?"#s','',$contentXML);
			$contentXML = preg_replace('#url="\d+/#s','url="media/',$contentXML);
			$contentXML = preg_replace('#url="etc/#s','url="media/',$contentXML);

			file_put_contents($newfile,$contentXML);
			// copy image
			// create media folder
			$mediaFolder = $tempFolder.DS.'media';
			mkdir($mediaFolder,0777);
			Dirs::copyDirectory(MEDIA_FOLDER.$fileName,$mediaFolder);

			// cover file
			$coverFile = ROOT.'appli'.DS.'medias'.DS.$fileName.DS.'frontcover.jpg';
			if(!file_exists($coverFile))
				$coverFile = PROFILES_FOLDER.$portal.DS.'epub'.DS.'cover.jpg';

			// ePub file
			$epubFile = EPUBS_FOLDER.$portal.DS.$fileName.'.epub';
			$cmd = array('teitoepub');
			// profiledir
			$cmd[] = '--profiledir='.PROFILES_FOLDER;
			// profile
			$cmd[] = '--profile='.$portal;
			// css
			$cmd[] = '--css='.PROFILES_FOLDER.$portal.DS.'epub'.DS.'epub.css';
			// cover
			$cmd[] = '--coverimage='.$coverFile;
			// publisher
			$cmd[] = '--publisher="xxxx"';
			// fileIn
			$cmd[] = $newfile;
			// fileOut
			$cmd[] = $epubFile;

			$cmd[] = '2>&1';
			$cmd = implode(' ', $cmd);

			// result lines
			exec($cmd,$result);

			$result = implode("\n",$result);
			// test BUILD SUCCESSFUL presence
			$matches = preg_match('#BUILD SUCCESSFUL#',$result);
			if(!$matches){
				$errorMsg = $result;
				$error = true;
			} else {
				// update ePub
				// metadata ----------------------------------------------------------
				$metadata = Tool::objectToArray(
					json_decode(file_get_contents(TEI_FOLDER.$portal.DS.$fileName.DS.'header.json'))
				);
				$EpubInsert = new EpubInsert($epubFile,$metadata);
				if($EpubInsert->init){
					$cl = new $portal();
					$cl->epub($EpubInsert);
					$EpubInsert->close();
				}
				// epubcheck
				$check = TOOLS_FOLDER.'epubcheck'.DS.'epubcheck.jar';
				$cmd = 'java -jar '.$check.' '.$epubFile.' 2>&1 | tee '.EPUBS_FOLDER.$portal.DS.$fileName.'_check.txt &';
				shell_exec($cmd);
			}
			Dirs::deleteDirectory($tempFolder);
		} else {
			$errorMsg = 'file Not exist';
			$error = true;
		}
		return array('error'=>$error,'errorMsg'=>$errorMsg);
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
