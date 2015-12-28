<?php
/**
* Simple Controler
* @package Micro
* @subpackage Controler
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class SimpleControler extends Controler implements IControler {

	/**
	* Route
	*/
	public function route(){
		// request
		$requestClName = REQUEST;
		$requestIns = new $requestClName();
		$this->request = $requestIns->getRequest();
		// locale
		Lang::defineLocale();
		// IP connexion
		if(!isset($_SESSION["IPCHECK"])){
			$Auth_user = new Auth_user();
			$Auth_user->checkIp();
		}
		Subscription::create();
		$this->_redirection();
	}
}
?>
