<?php
/**
* Admin_TEI_demo module - Out JSON
* @package zord
* @subpackage Module_admin
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Admin_TEI_demo extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array(
		'connect' => true,
		'name' => 'admin',
		'redirection' => array('module'=>'Admin','action'=>'connexion')
	);

	// Response -----------------------------------------------------------------
	public $response = 'JSON';

	// validation ---------------------------------------------------------------
	public $validation_filter = array(
		'xml' => FILTER_FLAG_NONE,
		'repository' => FILTER_SANITIZE_STRING,
		'folder' => FILTER_SANITIZE_NUMBER_INT
	);
	public $validation_filter_path = array();
	/**
	* Validation TEI file - RNG
	*
	* @return JSON
	*/
	public function validation() {
		$repository = $this->request['params']['repository'];
		$folder = $this->request['params']['folder'];
		if(Filter::validPortal($repository)){
			if(Filter::validEAN13($folder)){
				// Get all graphics
				$graphicsFiles = Dirs::globRecursive(MEDIA_FOLDER.$folder.DS.'*');
				$graphics = array();
				foreach($graphicsFiles as $gdile){
					$graphics[] = pathinfo($gdile,PATHINFO_BASENAME);
				}

				// check repository exists
				if(!file_exists(TEI_FOLDER.$repository))
					mkdir(TEI_FOLDER.$repository,0777);

				// clean tei folder
				$docFolder = TEI_FOLDER.$repository.DS.$folder;
				if(is_dir($docFolder) && $docFolder!=TEI_FOLDER.$repository.DS)
					Dirs::deleteContentsDirectory($docFolder);
				else
					mkdir($docFolder,0777);

				// del epub
				$epubFile = EPUBS_FOLDER.$repository.DS.$folder.'.epub';
				if(file_exists($epubFile))
					unlink($epubFile);

				// rng
				$file_rng = TOOLS_FOLDER.'tei_all.rng';
				$file = $docFolder.DS.$folder.'.xml';

				$this->request['params']['xml'] = Tool::clearNoteTEI($this->request['params']['xml']);
				file_put_contents($file,$this->request['params']['xml']);

				$msg = array();
				$er = Cmd::jing($repository,$folder);
				if($er!='')
					$msg[] = str_replace($file,'line',$er);

				// prefix tei:XX
				$doc = new DOMDocument();
				$xsl = new XSLTProcessor();
				$xsl->registerPHPFunctions();
				$doc->load(LIB_FOLDER.'xslt'.DS.'prefixTEI.xsl');
				$xsl->importStyleSheet($doc);

				try {
						@$doc->load($file);
						$tei = $xsl->transformToXML($doc);

						$result = array(
							'rng' => implode("\n",$msg),
							'graphics' => $graphics,
							'tei' => $tei
						);

						return $result;
				} catch (Exception $e) {
						echo 'Exception reçue : ',  $e->getMessage(), "\n";
						return $this->error(303,'Exception reçue : ',  $e->getMessage(), "\n");
				}
			} else {
				return $this->error(303,'Error file name');
			}
		} else {
			return $this->error(303,'Error repository name');
		}
	}

	// saveSource ---------------------------------------------------------------
	public $saveSource_filter = array(
		'structure' => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','sourceStucture')),
		'header' => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','sourceHeader')),
		'toc' => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','sourceToc')),
		'abstract' => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','sourceAbstract')),
		'ids' => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','sourceIds')),
		'fileName' => FILTER_SANITIZE_NUMBER_INT,
		'level' => FILTER_SANITIZE_NUMBER_INT,
		'repository' => FILTER_SANITIZE_STRING
	);
	public $saveSource_filter_path = array();
	/**
	* Save TEI source
	*
	* @return JSON
	*/
	public function saveSource() {
		$repository = $this->request['params']['repository'];
		$document = $this->request['params']['fileName'];
		$abstract = $this->request['params']['abstract'];
		$level = (int) $this->request['params']['level'];
		$ids = $this->request['params']['ids'];

		$solr = new Solr();

		$categories = array();
		$file = LIB_FOLDER.'zord'.DS.'demo'.DS.'categories.json';
		if(file_exists($file))
			$categories = Tool::objectToArray(json_decode(file_get_contents($file)));

		if(Filter::validPortal($repository)){
			if(Filter::validEAN13($document)){
				$docFolder = TEI_FOLDER.$repository.DS.$document.DS;

				// metadata ----------------------------------------------------------

				$metadata = Tei::parseHeader(
					$this->request['params']['header'],
					OPENURL.$document
				);

				$metadata = array_merge($metadata, array(
					'book' => $document,
					'library' => 'book',
					'level' => $level,
					'novelty' => false,
					'repository' => $repository
				));

				if(!isset($metadata['category']) || !array_key_exists($metadata['category'][0], $categories))
					$metadata['category'] = array($repository);

				// metadata save
				file_put_contents($docFolder.'header.json',Tool::json_encode($metadata));

				$solr->addBook($metadata);
				$creationDate = '';
				if(isset($metadata['creation_date']))
					$creationDate = $metadata['creation_date'];

				$partSave = array();

				$sequence = 1;
				// save content & create indexation
				foreach($this->request['params']['structure'] as $part){
					$file = $document.'_'.$part['partName'];

					file_put_contents($docFolder.$file.'.xml',Tool::clearTEI($part['element']));
					$indexation = array(
						'partName' => $part['partName'],
						'category' => $metadata['category'],
						'document' => $document,
						'contentType' => $part['contentType'],
						'file' => $file,
						'repository' => $repository,
						'level' => $level,
						'title' => $part['title'],
						'creationDate' => $creationDate,
						'sequence' => $sequence
					);
					$partSave[] = $indexation;
					$indexationFileName = 'f_'.$document.'_'.$part['partName'];
					$sequence++;
					file_put_contents(ROOT.'indexation'.DS.$indexationFileName.'.json',Tool::json_encode($indexation));
				}

				// toc
				file_put_contents($docFolder.'toc.xml',$this->request['params']['toc']);
				// header
				file_put_contents($docFolder.'header.xml',$this->request['params']['header']);
				// partSave
				file_put_contents($docFolder.'part_save.json',Tool::json_encode($partSave));
				// ids
				file_put_contents($docFolder.'ids.json',Tool::json_encode($ids));

				return array();
			} else {
				return $this->error(303,'Error file name');
			}
		} else {
			return $this->error(303,'No repository');
		}
	}

	// updateLevelSource ---------------------------------------------------------------
	public $updateLevelSource_filter = array(
		'fileName' => FILTER_SANITIZE_NUMBER_INT,
		'level' => FILTER_SANITIZE_NUMBER_INT,
		'repository' => FILTER_SANITIZE_STRING
	);
	public $updateLevelSource_filter_path = array();
	/**
	* Upadte level publication for TEI source
	*
	* @return JSON
	*/
	public function updateLevelSource() {
		$repository = $this->request['params']['repository'];
		$document = $this->request['params']['fileName'];
		$level = (int) $this->request['params']['level'];

		$solr = new Solr();
		if(is_dir(TEI_FOLDER.$repository)){
			$docFolder = TEI_FOLDER.$repository.DS.$document.DS;

			// metadata ----------------------------------------------------------
			$metadata = Tool::objectToArray(json_decode(file_get_contents($docFolder.'header.json')));
			$metadata['level'] = $level;
			$solr->addBook($metadata);
			// metadata save
			file_put_contents($docFolder.'header.json',Tool::json_encode($metadata));

			$partSave = Tool::objectToArray(json_decode(file_get_contents($docFolder.'part_save.json')));
			// save content & create indexation
			foreach($partSave as $indexation){
				$file = $document.'_'.$indexation['partName'];
				$indexation['level'] = $level;
				$indexationFileName = 'f_'.$document.'_'.$indexation['partName'];
				file_put_contents(ROOT.'indexation'.DS.$indexationFileName.'.json',Tool::json_encode($indexation));
			}
			return array();
		} else {
			return $this->error(303,'No repository');
		}
	}

	// updateNovelty ---------------------------------------------------------------
	public $updateNovelty_filter = array(
		'fileName' => FILTER_SANITIZE_NUMBER_INT,
		'novelty' => FILTER_VALIDATE_BOOLEAN,
		'repository' => FILTER_SANITIZE_STRING
	);
	public $updateNovelty_filter_path = array();
	/**
	* Upadte novelty publication
	*
	* @return JSON
	*/
	public function updateNovelty() {
		$repository = $this->request['params']['repository'];
		$document = $this->request['params']['fileName'];
		$novelty = $this->request['params']['novelty'];

		$solr = new Solr();
		if(is_dir(TEI_FOLDER.$repository)){
			$docFolder = TEI_FOLDER.$repository.DS.$document.DS;
			// metadata ----------------------------------------------------------
			$metadata = Tool::objectToArray(json_decode(file_get_contents($docFolder.'header.json')));
			$metadata['novelty'] = $novelty;
			$solr->addBook($metadata);
			// metadata save
			file_put_contents($docFolder.'header.json',Tool::json_encode($metadata));
			return array();
		} else {
			return $this->error(303,'No repository');
		}
	}
}
?>
