<?php
/**
* Log file simply
* @package Micro
* @subpackage Core
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Log {

	private static $_maxFileSize = 25000;

	/**
	* Write log message
	*
	* @param String $msg Message
	* @param String $fileName File name
	*/
	public static function set($msg, $fileName='system') {
		$logFile = LOGS_FOLDER.$fileName.'.log';
		$fileSize = 0;
		$content = '';
		if(file_exists($logFile)){
			$fileSize = filesize($logFile);
			$content = file_get_contents($logFile);
		}
		// compress file
		if($fileSize>self::$_maxFileSize){
			$countFiles = count(glob($logFile."*"))-1;
			$compressFile = $logFile.'.'.$countFiles.'.gz';
			while (file_exists($compressFile))
			$compressFile = $logFile.'.'.($countFiles++).'.gz';
			file_put_contents("compress.zlib://$compressFile", $content);
			unlink($logFile);
		}
		file_put_contents($logFile, date("[d-m-Y H:i:s]").' '.$msg.PHP_EOL.$content);
	}
}

?>
