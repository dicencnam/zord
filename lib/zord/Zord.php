<?php
/**
* Interface IZord
* @package zord
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
interface IZord {
	public function getStart();

	public function getPage($page);

	public function getBook($name,$part,$level,$title);

	public function getSearch();
}

/**
* Zord
* @package zord
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
abstract Class Zord {
	/**
	* Get header HTML
	*
	* @return string
	*/
	protected function _getHeader(){
		$langName = $_SESSION['switcher']['name'].'/header';
		Lang::load($langName);
		return Tpl::render('public/'.$_SESSION['switcher']['name'].'/header',Lang::get($langName));
	}

	/**
	* Get navigation HTML
	*
	* @return string
	*/
	protected function _getNavigation(){
		$langName = $_SESSION['switcher']['name'].'/navigation';
		Lang::load($langName);
		return Tpl::render('public/'.$_SESSION['switcher']['name'].'/navigation',Lang::get($langName));
	}

	/**
	* Get dialog HTML
	*
	* @return string
	*/
	protected function _getDialog(){
		$langName = $_SESSION['switcher']['name'].'/dialog';
		Lang::load($langName);
		return Tpl::render('public/'.$_SESSION['switcher']['name'].'/dialog',Lang::get($langName));
	}

	/**
	* Get footer HTML
	*
	* @return string
	*/
	protected function _getFooter(){
		$langName = $_SESSION['switcher']['name'].'/footer';
		Lang::load($langName);
		return Tpl::render('public/'.$_SESSION['switcher']['name'].'/footer',Lang::get($langName));
	}

	/**
	* Get header HTML - JS and CSS
	*
	* @return object JsCss
	*/
	protected function _JsCss_main(){
		$jscss = new JsCss();
		$jscss->setValue('string','PATH',BASEURL);
		$jscss->setValue('string','WEBSITE',$_SESSION["switcher"]["name"]);
		$jscss->setScript('js/dialog');
		$jscss->setScript('public/js/'.$_SESSION['switcher']['name'].'/main');
		$jscss->setLink('main');
		$jscss->setLink('main_small','screen and (max-width: 1135px)');
		$jscss->setLink('print','print');
		return $jscss;
	}

	/**
	* Get categories list
	*
	* @return array
	*/
	protected function _getCategories() {
		$file = LIB_FOLDER.'zord'.DS.$_SESSION['switcher']['name'].DS.'categories.json';
		if(file_exists($file))
			$categories = file_get_contents($file);
		else
			$categories = '{}';
		return Tool::objectToArray(json_decode($categories));
	}

	/**
	* Get metadata HTML
	*
	* @return string
	*/
	protected function _getData($name) {
		$file = TEI_FOLDER.$this->repository.DS.$name.DS.'header.json';
		$metadata = Tool::objectToArray(json_decode(file_get_contents($file)));
		$dc = new DC_html();
		$dc->set($metadata);

		$meta_html = new Meta_html();
		$meta_html->set($metadata);
		return $dc->get().$meta_html->get().
		'	<link rel="unapi-server" type="application/xml" title="unAPI" href="'.BASEURL.'Services/unapi"/>'.PHP_EOL;
	}

	/**
	* Get CSL list - Citation Style Langage
	*
	* @return array
	*/
	protected function _getCSLStyle() {
		$file = LIB_FOLDER.'zord'.DS.$_SESSION['switcher']['name'].DS.'csl_style.php';
		include($file);
		return $csl_style;
	}
}

?>
