<?php
/**
* Sitemap
* @package zord
* @subpackage Tools
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Sitemap {

	/**
	* sitemap XML header
	*
	* @var array
	*/
	protected $_content = array(
		'<?xml version="1.0" encoding="UTF-8"?>',
		'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
	);

	/**
	* Set website page informations
	*
	* @param string $url URL
	* @param string $lastmod Last modification
	* @param string $freq Frequency
	* @param string $prio Priority
	*/
	public function set($url, $lastmod = "", $freq = "monthly", $prio = "0.5") {
		$this->_content[] = '<url>';
		$this->_content[] = '<loc>'.htmlspecialchars($url).'</loc>';
		if($lastmod!="")
			$this->_content[] = '<lastmod>'.$lastmod.'</lastmod>';
		$this->_content[] = '<changefreq>'.$freq.'</changefreq>';
		$this->_content[] = '<priority>'.$prio.'</priority>';
		$this->_content[] = '</url>';
	}

	/**
	* Get sitemap XML
	*
	* @return string
	*/
	public function get() {
		$this->_content[] = '</urlset>';
		return implode("\n",$this->_content);
	}
}
?>
