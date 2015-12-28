<?php
/**
* Config
*/

require_once(CONFIG_FOLDER.'configZord.php');

// folders
define('CACHE_FOLDER',ROOT.'cache'.DS);
define('LOGS_FOLDER',ROOT.'log'.DS);
define('TEI_FOLDER',ROOT.'tei'.DS);
define('TEMP_FOLDER',ROOT.'temp'.DS);
define('EPUBS_FOLDER',ROOT.'epubs'.DS);
define('EPUBSADMIN_FOLDER',ROOT.'admin'.DS.'public'.DS.'epubs');
define('COUNTER_FOLDER',ROOT.'counter'.DS);
define('MEDIA_FOLDER',ROOT.'appli'.DS.'medias'.DS);
define('CSL_FOLDER',ROOT.'appli'.DS.'csl'.DS);
define('CSS_FOLDER',ROOT.'appli'.DS.'public'.DS.'css'.DS);
define('LANG_FOLDER',LIB_FOLDER.'locale'.DS);
define('VIEW_FOLDER',LIB_FOLDER.'view'.DS);
define('TOOLS_FOLDER',LIB_FOLDER.'tools'.DS);
define('PROFILES_FOLDER',LIB_FOLDER.'profiles'.DS);
define('SUBSCRIPTION_FOLDER',ROOT.'subscription'.DS);
define('COVERS_FOLDER',ROOT.'covers'.DS);

require_once(CONFIG_FOLDER.'config_appli.php');

if(isset($_SERVER["SERVER_PROTOCOL"]))
	define('BASEURL', strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://'.$_SERVER['HTTP_HOST'].PROJECT_FOLDER);


// ********************************************************************
?>
