<?php
/**
* subscription
* @package zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Subscription {

	/**
	 * Instance singleton
	 *
	 * @var Subscription
	 */
	private static $_instance = null;

	/**
	* Valid books
	*
	* @var Array
	*/
	protected $books = null;

	/**
	* Constructor
	*
	*/
	final private function __construct(){ }

	/**
	 * Clone
	 *
	 */
	final public function __clone(){
		throw new Exception('Prohibition to duplicate Subscription');
	}

	final public static function create(){
		if (is_null(self::$_instance)){
			self::$_instance = new self();
			self::$_instance->load();
			return self::$_instance;
		} else {
			throw new Exception('Prohibition to recreate Subscription');
		}
	}

	public static function load(){
		self::$_instance->books = array();
		if(isset($_SESSION["user"]) && $_SESSION["user"]['subscription']>0) {
			$file = SUBSCRIPTION_FOLDER.'subs_'.$_SESSION['user']['id'].'.php';
			if(file_exists($file)){
				include($file);
				self::$_instance->books = $books;
			}
		}
	}

	/**
	* Valid book
	*
	*/
	public static function validBook($book) {
		if($_SESSION["user"]['subscription']==0)
			return true;
		else if(in_array($book,self::$_instance->books))
			return true;
		return false;
	}

	/**
	* Get books
	*
	*/
	public static function getBooks(){
		return self::$_instance->books;
	}

	/**
	* Set Books
	*
	*/
	public static function setBooks($books){
		if(isset($_SESSION["user"]) && $_SESSION["user"]['subscription']>0) {
			$file = SUBSCRIPTION_FOLDER.'subs_'.$_SESSION['user']['id'].'.php';
			// unique
			$books = array_unique($books);
			// size <= $_SESSION["user"]['subscription']
			if(count($books)>$_SESSION["user"]['subscription'])
				$books = array_slice($books, 0, $_SESSION["user"]['subscription']);
				$vars = array(
						'books' => array(
						'type' => 'array',
						'comment' => 'subscription user:'.$_SESSION['user']['id'],
						'val' => $books
					)
				);
			Config::saveToPHP($file,$vars, 'subscription');
			self::$_instance->load();
		}
	}
}

?>
