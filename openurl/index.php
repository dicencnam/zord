<?php
/**
* OpenURL
* @package zord
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

if(isset($_GET['id'])){
	$id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
	if($id!=null){
		define('DS',DIRECTORY_SEPARATOR);
		define('ROOT',dirname(dirname(__file__)).DS);
		define('LIB_FOLDER',ROOT.'lib'.DS);
		require_once(ROOT.'config'.DS.'config_solr.php');
		$options = array (
			'hostname' => SOLR_SERVER_HOSTNAME,
			'path' => SOLR_SERVER_PATH,
			'wt' => 'json',
			//'login' => SOLR_SERVER_USERNAME,
			//'password' => SOLR_SERVER_PASSWORD,
			'port' => SOLR_SERVER_PORT,
		);

		$client = new SolrClient($options);
		$SolrQuery = new SolrQuery();
		$SolrQuery->setQuery('library_s:book AND level_i:0 AND book_s:'.$id);
		$SolrQuery->setStart(0);
		$SolrQuery->setRows(1);
		$SolrQuery->addField('book_s')
		->addField('repository_s');
		$response = $client->query($SolrQuery);
		$responseArr = $response->getResponse();

		if($responseArr['response']['numFound']>0){
			require_once(LIB_FOLDER.'zord'.DS.'websites.php');
			$doc = $responseArr['response']['docs'][0];
			// todo plusieurs URL !
			$url = $websitesURL[$doc['repository_s']].'/'.$doc['book_s'];
			header("HTTP/1.1 303 See Other",TRUE,303);
			header("location: ".$url);
			exit();
		}
	}
}
// book not exist error 404
header('HTTP/1.0 404 No Content');
echo <<<EOD
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Error Not Found</title>
</head>
<body style="text-align:center;font-family:sans;">
<h1>Error 404</h1>
<h2>The resource does not exist</h2>
</body>
</html>
EOD;
exit();

?>
