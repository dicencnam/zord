<?php
/**
* Admin user module - Out JSON
* @package zord
* @subpackage Module_client
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class AdminUser extends Module {

	// Auth ---------------------------------------------------------------------
	public $auth = array(
		'connect' => true,
		'name' => 'USER_ADMINNISTRATION',
		'redirection' => array('module'=>'Start','action'=>'index')
	);

	// Response -----------------------------------------------------------------
	public $response = 'JSON';

	// getSubscription -------------------------------------------------------------------
	public $getSubscription_filter = array();
	public $getSubscription_filter_path = array();
	/**
	* Get subscription books
	*
	* @return HTML
	*/
	public function getSubscription() {
		if(isset($_SESSION["connect_USER_ADMINNISTRATION"]) && $_SESSION["connect_USER_ADMINNISTRATION"]===true){

			$docsHtml = '';
			$portalTabs = '';

			$langName = $_SESSION['switcher']['name'].DS.'subscription';
			Lang::load($langName);
			$lang = Lang::get($langName);
			include(CONFIG_FOLDER.'config_portals.php');

			foreach ($_SESSION['user']['websites'] as $portal) {
				$portalTabs .= '<div data-tab="'.$portal.'" class="tab"><div class="frame_title">'.$websitesNames[$portal].'</div></div>';
				$solr = new Solr();
				$paramQuery = 'library_s:book AND repository_s:'.$portal.' AND level_i:0';
				$response = $solr->getBooks(0,10000,array(),$paramQuery);
				$Table = new TableHTML(array(
					'checkline' => true,
					'tpl' => array(
						'book_s',
						'creator_ss',
						'title',
						'editor_ss',
						'date_i'
					)
				));
				foreach($response['response']['docs'] as $book){
						$Table->set($book);
				}
				$docsHtml .= Tpl::render('public/'.$_SESSION['switcher']['name'].'/subscription_portal',
					array(
						'lang' => $lang,
						'name' => $portal,
						'docsHTML' => $Table->get()
					)
				);
				$tabs = '<div class="tabs">'.$portalTabs.'</div>';
				$frames = '<div class="panels">'.$docsHtml.'</div>';
			}
			return array(
				'content'				=> Tpl::render('public/'.$_SESSION['switcher']['name'].'/subscription',
															array(
																'lang' => $lang,
																'panelsTabs' => $tabs.$frames,
																'counter' => $_SESSION["user"]['subscription']
															)
														),
				'books' 				=> Subscription::getBooks(),
				'subscription' 	=> $_SESSION["user"]['subscription']
			);
		}
		return $this->error(303,'');
	}

	// setSubscription -------------------------------------------------------------------
	public $setSubscription_filter = array(
		'books' => array('filter' => FILTER_CALLBACK, 'options' => array('Filter','json'))
	);
	public $setSubscription_filter_path = array();
	/**
	* Set subscription books
	*
	* @return HTML
	*/
	public function setSubscription() {
		if(isset($_SESSION["connect_USER_ADMINNISTRATION"]) && $_SESSION["connect_USER_ADMINNISTRATION"]===true){
			$books = Tool::objectToArray($this->request['params']['books']);
			Subscription::setBooks($books);
			return array();
		}
		return $this->error(303,'');
	}
}
?>
