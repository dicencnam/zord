<?php
/**
* Solr
* @package zord
* @subpackage tools
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Solr {

	/**
	* Solr connexion options
	*
	* @var array
	*/
	protected $options = null;

	/**
	* Constructor
	*/
	public function __construct(){
		require_once(ROOT.'config'.DS.'config_solr.php');
		$this->options = array (
			'hostname' => SOLR_SERVER_HOSTNAME,
			'path' => SOLR_SERVER_PATH,
			'wt' => 'json',
			//'login' => SOLR_SERVER_USERNAME,
			//'password' => SOLR_SERVER_PASSWORD,
			'port' => SOLR_SERVER_PORT,
		);
	}

	/**
	* Add book part
	*
	* @param string $book ISBN of the book
	* @param string $file file name (no extension)
	* @param string $repository Portal name
	* @param integer $level Publication level (0 issued, 1 draft)
	* @param string $title Part title
	* @param integer $date Original creation date
	* @param string $content Part text
	*/
	public function addPart($book,$file,$repository,$level,$title,$date,$contentType,$sequence,$category,$content){
		$client = new SolrClient($this->options);
		$doc = new SolrInputDocument();
		$doc->addField('id',$file.'_'.$repository);
		$doc->addField('file_s',$file);
		$doc->addField('book_s', $book);
		$doc->addField('repository_s', $repository);
		$doc->addField('level_i', $level);
		$doc->addField('contentType_i', $contentType);
		$doc->addField('sequence_i', $sequence);
		$doc->addField('date_i', $this->setDateInteger($date));
		$doc->addField('title_s', $title);
		foreach ($category as $cat)
			$doc->addField('category_ss', $cat);
		foreach($this->explodeByStringLength($content) as $t)
			$doc->addField('content', $t);
		$updateResponse = $client->addDocument($doc);
		$response = $updateResponse->getResponse();
		$client->commit();
	}

	/**
	* Add book
	*
	* @param array $data Data of the book
	*/
	public function addBook($data){
		$client = new SolrClient($this->options);
		$doc = new SolrInputDocument();
		$doc->addField('id','book_'.$data['book'].'_'.$data['repository']);
		$doc->addField('library_s',$data['library']);
		$doc->addField('book_s', $data['book']);
		$doc->addField('repository_s', $data['repository']);
		$doc->addField('date_publication_portal_dt', $this->setDate(date("d-m-Y")));
		if(isset($data['date']))
			$doc->addField('date_i', $this->setDateInteger($data['date']));
		$doc->addField('level_i', $data['level']);
		$doc->addField('title_s', $data['title']);
		$doc->addField('novelty_b', $data['novelty']);
		foreach($data['creator'] as $creator)
			$doc->addField('creator_ss', $creator);

		if(isset($data['editor'])){
			foreach($data['editor'] as $editor)
				$doc->addField('editor_ss', $editor);
		}

		foreach ($data['category'] as $cat)
			$doc->addField('category_ss', $cat);

		if(isset($data['category_number']))
			$doc->addField('category_number_i', (int) $data['category_number']);

		if(isset($data['subtitle']))
			$doc->addField('subtitle_s', $data['subtitle']);

		if(isset($data['relation']))
			$doc->addField('relation_s', $data['relation']);

		if(isset($data['creation_date']))
			$doc->addField('creation_date_i', $this->setDateInteger($data['creation_date']));

		if(isset($data['creation_date_after']))
			$doc->addField('creation_date_after_i', $this->setDateInteger($data['creation_date_after']));

		if(isset($data['langusage']))
			$doc->addField('langusage_s', $data['langusage']);

		if(isset($data['ref_url']))
			$doc->addField('ref_url_s', $data['ref_url']);

		$updateResponse = $client->addDocument($doc);
		$response = $updateResponse->getResponse();
		$client->commit();
	}

	/**
	* Search
	*
	* @param array $query
	* @return array
	*/
	public function search($query){
		$client = new SolrClient($this->options);
		$SolrQuery = new SolrQuery();
		$this->_queryPrepare($SolrQuery,$query);
		$SolrQuery->setStart(0);
		$SolrQuery->setRows(200);
		$SolrQuery->addField('id')
		->addField('book_s')
		->addField('repository_s')
		->addField('title_s')
		->addField('sequence_i')
		->addField('category_ss')
		->addField('file_s');
		$SolrQuery->addHighlightField('content');
		$SolrQuery->setHighlightSimplePre('<b>');
		$SolrQuery->setHighlightSimplePost('</b>');
		$SolrQuery->setHighlightSnippets(100000);
		$SolrQuery->setHighlightFragsize(200);
		$SolrQuery->setHighlight(true);
		$query_response = $client->query($SolrQuery);
		return $query_response->getResponse();
	}

	/**
	* Get books title
	*
	* @param string $paramQuery
	* @return array
	*/
	public function getBooksTitle($paramQuery){
		$fields = array(
			'repository_s',
			'book_s',
			'level_i',
			'novelty_b',
			'title_s',
			'subtitle_s',
			'date_i'
		);

		$SortBooks = new SortBooks;
		$response = $this->getBooks(0,10000,$fields,$paramQuery);
		// $response = $SortBooks->sortDate($response);

		$__documents = array();
		$documents = array();

		foreach($response['response']['docs'] as $doc){
			if(!isset($__documents[$doc['repository_s']])){
				$__documents[$doc['repository_s']] = array();
				$documents[$doc['repository_s']] = array();
			}

			@$__documents[$doc['repository_s']][] = array('book_s'=>$doc['book_s'],'title'=>$this->getTitle($doc),'level'=>$doc['level_i'],'novelty'=>$doc['novelty_b'],'date_i'=>$doc['date_i']);
		}

		foreach($__documents as $repository => $books){
			$b = $SortBooks->sortDate($books);
			$b = array_reverse($b);
			foreach($b as $book)
				$documents[$repository][$book['book_s']] = $book;
		}

		return $documents;
	}

	/**
	* Get books
	*
	* @param integer $start
	* @param integer $row
	* @param array $addFields
	* @param string $paramQuery
	* @return array
	*/
	public function getBooks($start=0,$row=30,$addFields=array(),$paramQuery=null){
		// level
		$level = ' AND level_i:0';
		if($_SESSION['level']>0)
			$level = ' AND level_i:[0 TO 1]';

		if($paramQuery==null)
			$paramQuery = 'library_s:book AND repository_s:'.$_SESSION['switcher']['name'].$level;

		$client = new SolrClient($this->options);
		$query = new SolrQuery();
		$query->setQuery($paramQuery);
		foreach($addFields as $field)
			$query->addField($field);
		$query->setStart($start);
		$query->setRows($row);
		$query_response = $client->query($query);
		return $query_response->getResponse();
	}

	/**
	* Get books for frieze
	*
	* @return array
	*/
	public function getBooksFrieze(){
		$fields = array(
			'id',
			'book_s',
			'category_ss',
			'category_number_i',
			'date_i',
		//	'repository_s',
			'title_s',
			'editor_ss',
			'creator_ss',
			'file_s',
			'level_i',
			'subtitle_s',
			'relation_s',
			'creation_date_i',
			'creation_date_after_i',
		);

		// $paramQuery = array('library_s:book');

		// if($_SESSION['level']>0)
		// 	$paramQuery[] = 'level_i:[0 TO 1]';
		// else
		// 	$paramQuery[] = 'level_i:0';
		//
		// if(isset($_SESSION["user"]) && count($_SESSION["user"]['websites'])>1){
		// 	$paramQuery[] = 'repository_s:('.implode(' OR ',$_SESSION["user"]['websites']).')';
		// } else {
		// 	$paramQuery[] = 'repository_s:'.$_SESSION['switcher']['name'];
		// }
		//
		// $response = $this->getBooks(0,10000,$fields,implode(" AND ", $paramQuery));

		$response = $this->getBooks(0,10000,$fields);

		$documents = array('nosource'=>array());
		foreach($response['response']['docs'] as $doc){
			if(isset($doc['creation_date_i'])){
				if(!isset($documents[$doc['creation_date_i']]))
					$documents[$doc['creation_date_i']] = array();
				$documents[$doc['creation_date_i']][] = $doc;
			} else {
				$documents['nosource'][] = $doc;
			}
		}
		return $documents;
	}

	/**
	* Delete book
	*
	* @param string $book ISBN of the book
	* @param string $repository Portal name
	*/
	public function delBook($book,$repository){
		$client = new SolrClient($this->options);
		$client->deleteByQuery('repository_s:'.$repository.' AND book_s:'.$book);
		$client->commit();
	}

	/**
	* Explode text - 9000 chars
	*
	* @param string $string
	* @return array
	*/
	protected function explodeByStringLength($string){
		$maxLineLength = 9000;
		$string = trim(preg_replace('#\s{2,}#s',' ',htmlspecialchars_decode(strip_tags($string))));
		return $array = explode( "\n", wordwrap( $string, $maxLineLength));
	}

	/**
	* Set date "Y-m-d\TH:i:s.z\Z"
	*
	* @param string $date
	* @return string
	*/
	protected function setDate($date){
		$_d = explode('-',$date);
		if(count($_d)==1)
			$date = $date.'-01-01';
		$dateTime = new DateTime($date);
		return $dateTime->format("Y-m-d\TH:i:s.z\Z");
	}

	/**
	* Set year date
	*
	* @param string $date
	* @return integer Year
	*/
	protected function setDateInteger($date){
		$date = explode('-',$date);
		return (int) $date[0];
	}

	/**
	* Get title
	*
	* @param array $data
	* @param string
	*/
	protected function getTitle($data){
		$subtitle = '';
		if(isset($data['subtitle_s']))
			$subtitle = '. '.$data['subtitle_s'];
		$title = '';
		if(isset($data['title_s']))
			$title = $data['title_s'];
		return $title.$subtitle;
	}

	/**
	* Query prepare
	*
	* @param object $SolrQuery SolrQuery object
	* @param array $query
	*/
	protected function _queryPrepare($SolrQuery,$query){
		$queryString = array();

		$filterQuery = array('level_i:0');

		$category = array();

		foreach($query['query'] as $q){
			$key = key($q);
			if($key=='category_ss')
				$category[] = 'category_ss:'.$q[$key];
			else if($key=='text')
				$queryString[] = $key.':'.$q[$key];
			else
				$filterQuery[] = $key.':'.$q[$key];
		}
		$countCateg = count($category);
		if($countCateg>0){
			if($countCateg==1)
				$queryString[] = $category[0];
			else
				$filterQuery[] = implode(' OR ',$category);
		}

		foreach($query['range'] as $q){
			$key = key($q);
			if($q[$key]['max']!='')
				$filterQuery[] = $key.':['.$q[$key]['min'].' TO '.$q[$key]['max'].']';
		}

		$queryString = implode(' AND ',$queryString);

		$SolrQuery->setQuery($queryString);

		foreach ($filterQuery as $value) {
			$SolrQuery->addFilterQuery($value);
		}
	}
}
?>
