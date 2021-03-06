<?php
/**
* demo portal
* @package zord
* @subpackage portal
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class demo extends Zord implements IZord {

	/**
	* Portal idenditifiant
	*
	* @var String
	*/
	public $repository = 'demo';

	/**
	* Portal name
	*
	* @var String
	*/
	public $title = 'Démo';

	/**
	* Pages list
	*
	* @var array
	*/
	public $pages = array('presentation','subscription','contact','help','search');

	/**
	* Specific pages list
	*
	* @var array
	*/
	public $spcPages = array('search','marker','notices','adminconnect','admin');

	/**
	* Get home page
	*
	* @return string
	*/
	public function getStart() {
		$solr = new Solr();
		$response = $solr->getBooks(0,10000);
		$books = $this->sortBooks($response['response']['docs']);
		$jscss = $this->_JsCss_main();
		$jscss->setLink('table');
		$jscss->setScript('js/tablesort.min');
		$jscss->setScript('public/js/'.$_SESSION['switcher']['name'].'/start');
		$langName = $_SESSION['switcher']['name'].'/start_books';
		Lang::load($langName);
		return Tpl::render('public/'.$_SESSION['switcher']['name'].'/start',
		array(
			'title' => $this->title,
			'jscss' => $jscss->get(),
			'navigation' => $this->_getNavigation(),
			'header' => $this->_getHeader(),
			'content' => Tpl::render('public/'.$_SESSION['switcher']['name'].'/start_books',array(
				'lang'=>Lang::get($langName),
				'books'=> $books)
			),
			'footer' => $this->_getFooter(),
			'dialog' => $this->_getDialog()
			)
		);
	}

	/**
	* Get page
	*
	* @param String $page Page name
	* @return string
	*/
	public function getPage($page,$data) {
		if(in_array($page,$this->spcPages)){
			return call_user_func(array(&$this, 'get'.ucfirst($page)),$data);
		} else {
			$tpl = 'public'.DS.$_SESSION['switcher']['name'].DS.'pages'.DS.LANG.DS.$page;
			$file = VIEW_FOLDER.$tpl.'.php';
			if(file_exists($file)){
				$jscss = new JsCss();
				$jscss = $this->_JsCss_main();
				return Tpl::render('public/'.$_SESSION['switcher']['name'].'/start',
				array(
					'jscss' => $jscss->get(),
					'title' => $this->title,
					'navigation' => $this->_getNavigation(),
					'header' => $this->_getHeader(),
					'content' => Tpl::render($tpl),
					'footer' => $this->_getFooter(),
					'dialog' => $this->_getDialog()
					)
				);
			}
			return array(
				'__error__' => true,
				'code' => 404,
				'message' => ''
			);
		}
	}

	/**
	* Get book page
	*
	* @param String $name Book name - ISBN identifier
	* @param String $part Part book name
	* @param String $level Level publication
	* @return string
	*/
	public function getBook($name,$part,$level,$title) {
		$partString = $part;
		if($part==null){
			$part = 'home';
			$partString = '';
		}

		$clLevel = '';
		if($level)
			$clLevel = ' class="draft"';

		$folder = TEI_FOLDER.$this->repository.DS.$name.DS;

		$EncodeTEI = new EncodeTEI();

		$jscss = $this->_JsCss_main();
		$jscss->setScript('public/js/'.$_SESSION['switcher']['name'].'/book');
		$jscss->setScript('js/XSLDom');
		$jscss->setScript('js/XMLWrite');
		$cssFileName = $EncodeTEI->getCSS($_SESSION['switcher']['name']);
		$jscss->setLink($cssFileName);
		$jscss->setLink($cssFileName.'_print','print');
		$jscss->setValue('integer','BOOK',$name);
		$jscss->setValue('string','PART',$partString);
		$jscss->setValue('string','TITLE',htmlspecialchars($title, ENT_QUOTES));
		$jscss->setValue('string','IDS',$EncodeTEI->getELS());

		$langName = $_SESSION['switcher']['name'].'/book';
		Lang::load($langName);

		return Tpl::render('public/'.$_SESSION['switcher']['name'].'/book',
			array(
				'lang' => Lang::get($langName),
				'title' => $this->title,
				'data' => $this->_getData($name),
				'jscss' => $jscss->get(),
				'unapi' => $name,
				'header' => $this->_getHeader(),
				'navigation' => $this->_getNavigation(),
				'level' => $clLevel,
				'toc' => file_get_contents($folder.'toc.xml'),
				'tei' => $EncodeTEI->getXML($folder.$name.'_'.$part.'.xml'),
				'dialog' => $this->_getDialog(),
				'footer' => $this->_getFooter()
			)
		);
	}

	/**
	* Get search page
	*
	* @return string
	*/
	public function getSearch($data) {
		$langName = $_SESSION['switcher']['name'].'/categories';
		Lang::load($langName);
		$searchValue = array(
			'categories' => $this->_getCategories(),
			'categoriesLang' => Tool::objectToArray(Lang::get($langName))
		);

		$langName = $_SESSION['switcher']['name'].DS.'search';
		Lang::load($langName);
		$Solr = new Solr();
		$documents = $Solr->getBooksFrieze();
		$jscss = $this->_JsCss_main();
		$jscss->setScript('js/TableHTML');
		$jscss->setScript('js/OccHTML');
		$jscss->setScript('js/frieze');
		$jscss->setScript('js/tablesort.min');
		$jscss->setScript('public/js/'.$_SESSION['switcher']['name'].'/search');
		$jscss->setLink('search');

		if(isset($data['default']))
			$jscss->setValue('boolean','SEARCHLAST',true);
		else
			$jscss->setValue('boolean','SEARCHLAST',false);

		$jscss->setValue('json','DOCS',json_encode($documents));

		return Tpl::render('public/'.$_SESSION['switcher']['name'].'/start',
			array(
				'jscss' => $jscss->get(),
				'title' => $this->title,
				'navigation' => $this->_getNavigation(),
				'header' => $this->_getHeader(),
				'content' => Tpl::render('public/'.$_SESSION['switcher']['name'].'/search',
						array(
							'lang'=>Lang::get($langName),
							'search'=>$searchValue
						)),
				'footer' => $this->_getFooter(),
				'dialog' => $this->_getDialog()
			)
		);
	}

	/**
	* Get marker page
	*
	* @return string
	*/
	public function getMarker() {
		$langName = $_SESSION['switcher']['name'].DS.'marker';
		Lang::load($langName);
		$langs = Lang::get($langName);
		$jscss = $this->_JsCss_main();
		$jscss->setValue('string','LABEL_ADDNOTE',$langs->label_addnote);
		$jscss->setValue('string','LABEL_DELCITATION',$langs->label_delcitation);
		$jscss->setLink('marker');
		$jscss->setScript('js/xmldom');
		$jscss->setScript('js/citeproc');
		$jscss->setScript('js/marker');
		$data = array(
			'csl' => $this->_getCSLStyle(),
			'lang' => $langs
		);
		return Tpl::render('public/'.$_SESSION['switcher']['name'].'/start',
			array(
				'jscss' => $jscss->get(),
				'title' => $this->title,
				'navigation' => $this->_getNavigation(),
				'header' => $this->_getHeader(),
				'dialog' => $this->_getDialog(),
				'content' =>
				Tpl::render('public/'.$_SESSION['switcher']['name'].'/marker',$data),
				'footer' => $this->_getFooter()
			)
		);
	}

	/**
	* Get notices page
	*
	* @return string
	*/
	public function getNotices() {
		$langName = $_SESSION['switcher']['name'].DS.'notices';
		Lang::load($langName);
		$jscss = new JsCss();
		$jscss->setValue('string','PATH',BASEURL);
		$jscss->setValue('string','PORTAL',$_SESSION["switcher"]["name"]);
		$jscss->setLink('main');
		$jscss->setLink('table');
		$jscss->setScript('public/js/'.$_SESSION['switcher']['name'].'/main');
		$jscss->setScript('js/tablesort.min');
		$jscss->setScript('js/notices');
		$solr = new Solr();
		$response = $solr->getBooks(0,10000);
		$books = array('docs'=>array(),'novelty'=>array());

		foreach($response['response']['docs'] as $book){
			if($book['novelty_b'])
				$books['novelty'][] = $book;
			else
				$books['docs'][] = $book;
		}

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
		foreach($books['docs'] as $key => $book){
				$Table->set($book);
		}
		$docsHtml = $Table->get();

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
		foreach($books['novelty'] as $key => $book){
				$Table->set($book);
		}
		$noveltyHtml = $Table->get();

		return Tpl::render('public/'.$_SESSION['switcher']['name'].'/notices',
			array(
				'lang' => Lang::get($langName),
				'title' => $this->title,
				'jscss' => $jscss->get(),
				'header' => $this->_getHeader(),
				'navigation' => $this->_getNavigation(),
				'docsHtml' => $docsHtml,
				'noveltyHtml' => $noveltyHtml
			)
		);
	}

	/**
	* Get page Admin connexion
	*
	* @return string
	*/
	public function getAdminconnect() {
		$langName = $_SESSION['switcher']['name'].DS.'adminconnect';
		Lang::load($langName);
		$langs = Lang::get($langName);
		$jscss = $this->_JsCss_main();
		return Tpl::render('public/'.$_SESSION['switcher']['name'].'/start',
			array(
				'jscss' => $jscss->get(),
				'title' => $this->title,
				'navigation' => $this->_getNavigation(),
				'header' => $this->_getHeader(),
				'dialog' => $this->_getDialog(),
				'content' =>
				Tpl::render('public/'.$_SESSION['switcher']['name'].'/adminconnect',array('lang' => $langs)),
				'footer' => $this->_getFooter()
			)
		);
	}

	/**
	* Get page admin
	*
	* @return string
	*/
	public function getAdmin() {
		if(isset($_SESSION["connect_USER_ADMINNISTRATION"]) &&
		$_SESSION["connect_USER_ADMINNISTRATION"]===true){
			$langName = $_SESSION['switcher']['name'].DS.'admin';
			Lang::load($langName);
			$langs = Lang::get($langName);
			$jscss = $this->_JsCss_main();
			$jscss->setLink('table');
			$jscss->setScript('js/tablesort.min');
			$jscss->setScript('public/js/'.$_SESSION['switcher']['name'].'/admin');
			// subscription
			$sub = false;
			if(isset($_SESSION["user"]) && $_SESSION["user"]['subscription']>0)
				$sub = true;

			$jscss->setValue('boolean','SUBSCRIPTION',$sub);
			$data = array(
				'lang' => $langs
			);
			return Tpl::render('public/'.$_SESSION['switcher']['name'].'/start',
				array(
					'jscss' => $jscss->get(),
					'title' => $this->title,
					'navigation' => $this->_getNavigation(),
					'header' => $this->_getHeader(),
					'dialog' => $this->_getDialog(),
					'content' =>
					Tpl::render('public/'.$_SESSION['switcher']['name'].'/admin',$data),
					'footer' => $this->_getFooter()
				)
			);
		} else {
			return array(
				'__error__' => true,
				'code' => 404,
				'message' => ''
			);
		}
	}

	/**
	* Get connexion page
	*
	* @return string
	*/
	public function getConnexion($lasthref) {
		$langName = $_SESSION['switcher']['name'].DS.'connexion';
		Lang::load($langName);
		$jscss = $this->_JsCss_main();
		return Tpl::render('public/'.$_SESSION['switcher']['name'].'/start',
			array(
				'jscss' => $jscss->get(),
				'title' => $this->title,
				'navigation' => $this->_getNavigation(),
				'header' => $this->_getHeader(),
				'content' => Tpl::render(
					'public/'.$_SESSION['switcher']['name'].'/connexion',
						array(
							'lasthref' => $lasthref,
							'lang' => Lang::get($langName),
						)
					),
				'footer' => $this->_getFooter(),
				'dialog' => $this->_getDialog()
				)
		);
	}

	/**
	* Get motor page - sitemap
	*
	* @return string
	*/
	public function getMotor(){
		$sitemp = new Sitemap();
		foreach($this->pages as $page)
			$sitemp->set(BASEURL.'page/'.$page);

		$Solr = new Solr();
		$books = $Solr->getBooks(0,1000000,array('book_s'));
		foreach($books['response']['docs'] as $book)
			$sitemp->set(BASEURL.$book['book_s'],'', "monthly",'1');

		return $sitemp->get();
	}

	/**
	* Get citation for exportation
	*
	* @param String $content
	* @return string
	*/
	public function exportCitation($content){
		$content = preg_replace('#<span class="marker-del">-</span>#us','',$content);
		return Tpl::render('public/'.$_SESSION['switcher']['name'].'/citations',array(
			'title' => $this->title,
			'content'=>$content
		));
	}

	/**
	* Sort book
	*
	* @param Array $docs Documents
	* @return Array
	*/
	protected function sortBooks($docs){

		$books = array('source'=>array(),'nosource'=>array(),'novelty'=>array());

		foreach($docs as $book){
			if($book['novelty_b']){
				$books['novelty'][] = $book;
			} else {
				$cat = $book['category_ss'][0];
				if(isset($book['creation_date_i'])){
					if(!isset($books['source'][$cat]))
						$books['source'][$cat] = array();
					$books['source'][$cat][] = $book;
				} else {
					if(!isset($books['nosource'][$cat]))
						$books['nosource'][$cat] = array();
					$books['nosource'][$cat][] = $book;
				}
			}
		}

		$SortBooks = new SortBooks;

		$Table = new TableHTML(array(
			'tpl' => array(
				'creation_date_i',
				'creation_date_after_i',
				'creator_ss',
				'title',
				'editor_ss',
				'date_i'
			)
		));

		$books['source'] = $SortBooks->sortBycategCreationDate($books['source']);
		foreach($books['source'] as $key => $bks){
			foreach($bks as $book)
				$Table->set($book);
		}
		$books['source'] = $Table->get();

		$Table = new TableHTML(array(
			'tpl' => array(
				'creator_ss',
				'title',
				'editor_ss',
				'date_i'
			)
		));

		$books['nosource'] = $SortBooks->sortBycategDate($books['nosource']);

		foreach($books['nosource'] as $key => $bks){
			foreach($bks as $book)
				$Table->set($book);
		}
		$books['nosource'] = $Table->get();

		$Table = new TableHTML(array(
			'tpl' => array(
				'creation_date_i',
				'creation_date_after_i',
				'creator_ss',
				'title',
				'editor_ss',
				'date_i'
			)
		));
		foreach($books['novelty'] as $book)
			$Table->set($book);

		$books['novelty'] = $Table->get();

		return $books;
	}

	/**
	* Update epub
	*
	* @param object $Epub
	*/
	public function epub($Epub){

	}
}
?>
