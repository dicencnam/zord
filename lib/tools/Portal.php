<?php

/**
* Portal Zord manager
* @package zord
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Portal {

	/**
	* Directories list
	*
	* @var array
	*/
	public $directories = array(
		'appli/public/css/DEFAULT',
		'appli/public/css/DEFAULT/obf',
		'appli/public/css/DEFAULT/fonts',

		'appli/public/js/DEFAULT',

		'appli/public/img/DEFAULT',

		'lib/locale/LOCALE/DEFAULT',

		'lib/profiles/DEFAULT',

		'lib/view/public/DEFAULT',
		'lib/view/public/DEFAULT/pages',
		'lib/view/public/DEFAULT/pages/LOCALE',

		'lib/zord/DEFAULT'
	);

	/**
	* Language in Zord
	*
	* @var array
	*/
	public $zordLangs = array('fr-FR','en-EN');

	/**
	* Websites in Zord
	*
	* @var array
	*/
	public $websites = null;

	/**
	* Websites URL in Zord
	*
	* @var array
	*/
	public $websitesURL = null;

	/**
	* Websites Domain in Zord
	*
	* @var array
	*/
	public $websitesDomain = null;

	/**
	* Websites Domain in Zord
	*
	* @var array
	*/
	public $websitesDomain = null;

	/**
	* Files list
	*
	* @var array
	*/
	public $files = array(
		'admin/js/TEI_DEFAULT.js',
		'admin/js/TEITOHTML_DEFAULT.js',

		'appli/public/css/DEFAULT/frieze.css',
		'appli/public/css/DEFAULT/main.css',
		'appli/public/css/DEFAULT/main_small.css',
		'appli/public/css/DEFAULT/marker.css',
		'appli/public/css/DEFAULT/TEICSSNAMEFILE.css',
		'appli/public/css/DEFAULT/TEICSSNAMEFILE_print.css',
		'appli/public/css/DEFAULT/print.css',
		'appli/public/css/DEFAULT/search.css',
		'appli/public/css/DEFAULT/table.css',

		'appli/public/css/DEFAULT/font-awesome.css',
		'appli/public/css/DEFAULT/fonts/FontAwesome.otf',
		'appli/public/css/DEFAULT/fonts/fontawesome-webfont.eot',
		'appli/public/css/DEFAULT/fonts/fontawesome-webfont.svg',
		'appli/public/css/DEFAULT/fonts/fontawesome-webfont.ttf',
		'appli/public/css/DEFAULT/fonts/fontawesome-webfont.woff',
		'appli/public/css/DEFAULT/fonts/fontawesome-webfont.woff2',

		'appli/public/css/DEFAULT/obf/.gitignore',

		'appli/public/js/DEFAULT/admin.js',
		'appli/public/js/DEFAULT/book.js',
		'appli/public/js/DEFAULT/main.js',
		'appli/public/js/DEFAULT/search.js',
		'appli/public/js/DEFAULT/start.js',

		'appli/public/img/DEFAULT/wait.gif',
		'appli/public/img/DEFAULT/search.png',

		'lib/locale/LOCALE/DEFAULT/admin.json',
		'lib/locale/LOCALE/DEFAULT/adminconnect.json',
		'lib/locale/LOCALE/DEFAULT/book.json',
		'lib/locale/LOCALE/DEFAULT/categories.json',
		'lib/locale/LOCALE/DEFAULT/connexion.json',
		'lib/locale/LOCALE/DEFAULT/dialog.json',
		'lib/locale/LOCALE/DEFAULT/footer.json',
		'lib/locale/LOCALE/DEFAULT/header.json',
		'lib/locale/LOCALE/DEFAULT/marker.json',
		'lib/locale/LOCALE/DEFAULT/navigation.json',
		'lib/locale/LOCALE/DEFAULT/notices.json',
		'lib/locale/LOCALE/DEFAULT/search.json',
		'lib/locale/LOCALE/DEFAULT/start_books.json',
		'lib/locale/LOCALE/DEFAULT/subscription.json',

		'lib/modules/admin/Admin_TEI_DEFAULT.php',

		'lib/profiles/DEFAULT/cover.jpg',
		'lib/profiles/DEFAULT/epub.css',

		'lib/view/public/DEFAULT/admin.php',
		'lib/view/public/DEFAULT/adminconnect.php',
		'lib/view/public/DEFAULT/book.php',
		'lib/view/public/DEFAULT/citations.php',
		'lib/view/public/DEFAULT/connexion.php',
		'lib/view/public/DEFAULT/dialog.php',
		'lib/view/public/DEFAULT/footer.php',
		'lib/view/public/DEFAULT/header.php',
		'lib/view/public/DEFAULT/marker.php',
		'lib/view/public/DEFAULT/navigation.php',
		'lib/view/public/DEFAULT/notices.php',
		'lib/view/public/DEFAULT/search.php',
		'lib/view/public/DEFAULT/start.php',
		'lib/view/public/DEFAULT/start_books.php',
		'lib/view/public/DEFAULT/subscription.php',
		'lib/view/public/DEFAULT/subscription_portal.php',

		'lib/view/public/DEFAULT/pages/LOCALE/xxx.php',

		'lib/zord/DEFAULT/DEFAULT.php',
		'lib/zord/DEFAULT/categories.json',
		'lib/zord/DEFAULT/csl_style.php'
	);

	/**
	* Delete files list
	*
	* @var array
	*/
	public $deleteFiles = array(
		'admin/js/TEI_DEFAULT.js',
		'admin/js/TEITOHTML_DEFAULT.js',
		'lib/modules/admin/Admin_TEI_DEFAULT.php'
	);

	/**
	* Constructor
	*/
	public function __construct() {
		include(CONFIG_FOLDER.'config_language.php');
		$this->zordLangs = $zordLangs;
		include(CONFIG_FOLDER.'config_portals.php');
		$this->websites = $websites;
		$this->websitesURL = $websitesURL;
		$this->websitesDomain = $websitesDomain;
		$this->websitesNames = $websitesNames;
	}

	/**
	* Save Portal configuration
	*
	* @param String $portalName Portal name
	* @param String $URL
	* @param String $domain
	*/
	private function _saveConfig() {
		Config::saveToPHP(
			CONFIG_FOLDER.'config_portals.php',
			array(
				'websites' => array(
					'type' => 'array',
					'comment' => 'Portal list',
					'val' => $this->websites
				),
				'websitesURL' => array(
					'type' => 'arraykey',
					'comment' => 'Portal list URL',
					'val' => $this->websitesURL
				),
				'websitesDomain' => array(
					'type' => 'arraykey',
					'comment' => 'Portal list domain',
					'val' => $this->websitesDomain
				),
				'websitesNames' => array(
					'type' => 'arraykey',
					'comment' => 'Portal list name',
					'val' => $this->websitesNames
				),
			),
			'Portals configuration'
		);
	}

	/**
	* Delete portal
	*
	* @param String $portalName Portal name
	* @param String $url
	*/
	public function delete($portalName,$url) {
		if(in_array($portalName,$this->websites))
			$this->websites = array_diff($this->websites, array($portalName));

		if(isset($this->websitesURL[$portalName]))
			unset($this->websitesURL[$portalName]);

		$portalDomainName = Tool::domainName($url);

		if(isset($this->websitesDomain[$portalDomainName]))
			unset($this->websitesDomain[$portalDomainName]);

		if(isset($this->websitesNames[$portalDomainName]))
			unset($this->websitesNames[$portalName]);

		$this->_saveConfig();

		// directories
		foreach ($this->directories as $sourceDir) {
			$destDir = $this->_getDest($portalName,$sourceDir);
			if($this->_isLocal($destDir)){
				foreach ($this->zordLangs as $locale) {
					$_destDir = $this->_getDestLocale($locale,$destDir);
					if(file_exists(ROOT.$_destDir))
						Dirs::deleteDirectory(ROOT.$_destDir);
				}
			} else {
				if(file_exists(ROOT.$destDir))
					Dirs::deleteDirectory(ROOT.$destDir);
			}
		}

		// files
		foreach ($this->deleteFiles as $sourcefile) {
			$destFile = $this->_getDest($portalName,$sourcefile);
			if(file_exists(ROOT.$destFile))
				unlink(ROOT.$destFile);
		}
	}

	/**
	* Create portal
	*
	* @param String $portalName Portal name
	* @param String $url
	* @param String $publisher
	* @param String $name
	* @return Boolean
	*/
	public function create($portalName,$url,$publisher,$name) {
		if(!isset($this->websites[$portalName])){
			$this->websites[] = $portalName;
			$this->websitesURL[$portalName] = $url;
			$portalDomainName = Tool::domainName($url);
			$this->websitesDomain[$portalDomainName] = $portalName;

			$this->websitesNames[$portalName] = htmlentities($name,ENT_QUOTES);

			$this->_saveConfig();

			// directories
			foreach ($this->directories as $sourceDir) {
				$destDir = $this->_getDest($portalName,$sourceDir);
				if($this->_isLocal($destDir)){
					foreach ($this->zordLangs as $locale) {
						$_destDir = $this->_getDestLocale($locale,$destDir);
						if(!file_exists(ROOT.$_destDir))
							mkdir(ROOT.$_destDir, 0777);
					}
				} else {
					if(!file_exists(ROOT.$destDir))
						mkdir(ROOT.$destDir,0777);
				}
			}

			// files
			foreach ($this->files as $sourcefile) {
				$destFile = $this->_getDest($portalName,$sourcefile);
				if($this->_isLocal($destFile)){
					foreach ($this->zordLangs as $locale) {
						$_destFile = $this->_getDestLocale($locale,$destFile);
						$_sourcefile = $this->_getDestLocale($locale,$sourcefile);
						file_put_contents(ROOT.$_destFile,$this->_filePrepare($portalName,$publisher,$_sourcefile));
					}
				} else {
					file_put_contents(ROOT.$destFile,$this->_filePrepare($portalName,$publisher,$sourcefile));
				}
			}
			return true;
		} else {
			return false;
		}
	}

	private function _filePrepare($portalName,$publisher,$file){
		$content = file_get_contents(ROOT.$file);
		$content = preg_replace("#__PORTALDEFAULT__#s",$portalName,$content);
		$content = preg_replace("#__PUBLISHERDEFAULT__#s",$publisher,$content);
		return $content;
	}

	private function _getDest($portalName,$file){
		return preg_replace(
			array("#DEFAULT#","#TEICSSNAMEFILE#"),
			array($portalName,TEICSSNAMEFILE),
			$file
		);
	}

	private function _getDestLocale($locale,$file){
		return preg_replace("#LOCALE#",$locale,$file);
	}

	private function _isLocal($file){
		if (strpos($file, 'LOCALE') !== false)
			return true;
		return false;
	}
}
?>
