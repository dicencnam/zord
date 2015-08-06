<?php
/**
* Services
* @package zord
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

// self to self dervice
if(!isset($_SERVER["SERVER_ADDR"])){
	define('DS',DIRECTORY_SEPARATOR);
	define('ROOT',dirname(dirname(__file__)).DS);
	define('LIB_FOLDER',ROOT.'lib'.DS);
	require_once(ROOT.'config'.DS.'config.php');
	require_once(LIB_FOLDER.'core'.DS.'Autoloader.php');
	Autoloader::start();
	$service = $_SERVER['argv'][1];
	switch ($service) {
		case 'teitoepub':
			$fileName = $_SERVER['argv'][2];
			$portal = $_SERVER['argv'][3];
			try {
				$Teitoepub = new Teitoepub();
				$result = $Teitoepub->create($fileName,$portal);
				$logContent = $fileName.' '.$portal.' ';
				if($result['error'])
					$logContent .= ":-(\n".$result['errorMsg']."\n\n";
				else
					$logContent .= ":-)\n\n";

				echo date("[d-m-Y H:i:s]").' '.$logContent;
			} catch (Exception $e) {
				echo date("[d-m-Y H:i:s]").' Exception:'.$e->getMessage()."\n";
			}
		break;
		case 'indexation':
			$start = time();
			$delay = 300-15;// cron time
			$wait = 3;

			$files = glob(ROOT.'indexation'.DS.'*', 0);
			foreach ($files as $file) {
				if(time()>$start+$delay){
					break;
				} else {
					if(file_exists($file)){
						$indexation = Tool::objectToArray(json_decode(file_get_contents($file)));
						$fileSource = TEI_FOLDER.$indexation['repository'].DS.$indexation['document'].DS.$indexation['file'].'.xml';
						if(file_exists($fileSource)){
							try {

								$doc = new DOMDocument();
								$xsl = new XSLTProcessor();
								$xsl->registerPHPFunctions();
								$doc->load(LIB_FOLDER.'xslt'.DS.'notes_end.xsl');
								$xsl->importStyleSheet($doc);
								$doc->load($fileSource);
								$tei = $xsl->transformToXML($doc);

								$solr = new Solr();
								$x = $solr->addPart(
									$indexation['document'],
									$indexation['file'],
									$indexation['repository'],
									$indexation['level'],
									$indexation['title'],
									$indexation['creationDate'],
									$indexation['contentType'],
									$indexation['sequence'],
									$indexation['category'],
									$tei
								);
								echo date("[d-m-Y H:i:s]").' Ok:'.$indexation['file']."\n";
							} catch (Exception $e) {
								echo date("[d-m-Y H:i:s]").' Exception:'.$e->getMessage()."\n";
							}
						} else {
							echo date("[d-m-Y H:i:s]").' Error [1]:'.$indexation['file']."\n";
						}
						unlink($file);
						sleep($wait);
					} else {
						echo date("[d-m-Y H:i:s]").' Error [2]:'.$file." not exist\n";
					}
				}
			}
		break;
		default:
		break;
	}
}
?>
