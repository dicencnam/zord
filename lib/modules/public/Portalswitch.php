<?php
/**
* Portal module - Out HTML
* @package zord
* @subpackage Module_client
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Portalswitch extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array('connect' => false);

	// Response -----------------------------------------------------------------
	public $response = 'HTML';

	// index --------------------------------------------------------------------
	public $index_filter = array();
	public $index_filter_path = array();
	/**
	* Home
	*
	* @return HTML
	*/
	public function index() {
		return "<html>
		<head>
		<script>window.location.href='../';</script>
		</head>
		</html>";
	}
}
?>
