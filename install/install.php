<?php

define('DS',DIRECTORY_SEPARATOR);
define('ROOT',dirname(dirname(__file__)).DS);
define('CONFIG_FOLDER',ROOT.'config'.DS);
define('LIB_FOLDER',ROOT.'lib'.DS);

include_once(LIB_FOLDER.'core/Configer.php');
include_once(LIB_FOLDER.'tools/Tool.php');
include_once(CONFIG_FOLDER.'definition.php');
include_once(CONFIG_FOLDER.'configZord.php');

$title = '<h1>'.ZORD.' '.ZORD_VERSION.'<br/>Installation</h1>'.PHP_EOL;
$referer = $_SERVER['HTTP_REFERER'];
$URL = substr($referer, 0, strrpos($referer, "/"));
$URL = substr($URL, 0, strrpos($URL, "/")).'/';

$Configer = new Configer();

if(isset($_POST['configer'])){
	try {
		// create DB
		$db = new PDO(
			trim($_POST['ORM_ADMIN_ENGINE']).":host=".trim($_POST['ORM_ADMIN_HOST']).";dbname=".trim($_POST['ORM_ADMIN_DBNAME']),
			trim($_POST['ORM_ADMIN_USERNAME']),
			trim($_POST['ORM_ADMIN_PASSWORD'])
		);
		$sql = file_get_contents(ROOT.'install/zord.sql');
		$qr = $db->exec($sql);

		// admin user insert
		$statement = $db->prepare("INSERT INTO admin(login, password) VALUES(:login, :password)");
		$statement->execute(array(
				"login" => trim($_POST['ADMINDEF_EMAIL']),
				"password" => hash('sha256', trim($_POST['APPLI_SALT']).trim($_POST['ADMINDEF_PASSWORD']))
		));

		$Configer->saveConfigs($configs,$_POST);
		// portals default
		$domain = Tool::domainName();
		$portals = '<?php
/* Portals configuration */

/* Portal list */
$websites = array (
	"demo",
);

/* Portal list URL */
$websitesURL = array (
	"demo" => "'.$URL.'appli/",
);

/* Portal list domain */
$websitesDomain = array (
	"'.$domain.'" => "demo",
);

$websitesNames = array (
	"demo" => "Démo Zord",
);
?>';
		file_put_contents(CONFIG_FOLDER.'config_portals.php', $portals);
		// langs
		$lang = "<?php
// Lang list code
\$zordLangs = array('fr-FR','en-EN');
// Lang list string
\$zordLangsString = array(
	'fr-FR' => 'FR',
	'en-EN' => 'EN'
);
?>";
		file_put_contents(CONFIG_FOLDER.'config_language.php', $lang);
		echo toHTMLPage($title.'<h2>Configuration Save</h2>');
	} catch (Exception $e) {
		echo toHTMLPage($title.'<h2>Error</h2><p class="error">Caught exception: '.$e->getMessage()."</p>");
	}
} else {
	// update config new install
	if(!file_exists(CONFIG_FOLDER.'config_appli.php')){
		$PROJECT_FOLDER = preg_replace('#http[s]*://'.preg_quote($_SERVER['HTTP_HOST']).'#', '', $referer);
		$PROJECT_FOLDER = substr($PROJECT_FOLDER, 0, strrpos($PROJECT_FOLDER, "/"));
		$PROJECT_FOLDER = substr($PROJECT_FOLDER, 0, strrpos($PROJECT_FOLDER, "/")).'/';
		$configs['appli']['definition']['APPLI_SALT']['value'] = 'SALT_'.uniqid();
		$configs['appli']['definition']['APPLI_TEICSSNAMEFILE']['value'] = 'TEI_'.uniqid();
		$configs['appli']['definition']['APPLI_OPENURL']['value'] = $URL.'openurl/?id=';
		$configs['admin']['definition']['ADMIN_PROJECT_FOLDER']['value'] = $PROJECT_FOLDER.'admin/';
		$configs['user']['definition']['USER_PROJECT_FOLDER']['value'] = $PROJECT_FOLDER.'appli/';
	}

	$defAdminUser= array(
		'ADMINDEF_EMAIL' => array(
			 'comment'=> 'Administrator email',
			 'type' => 'email',
			 'value' => ''
		),
		'ADMINDEF_PASSWORD' => array(
			 'comment'=> 'Administrator password',
			 'type' => 'string',
			 'value' => ''
		)
	);
	$admnistratorHTML = $Configer->toHTML($defAdminUser,'Administrator');
	$x = $Configer->loadConfigs($configs,'install.php',$admnistratorHTML);
	echo toHTMLPage($title.$x['ALL_CONFIGS_HTML']);
}

function toHTMLPage($html){
	return <<<EOT
<!DOCTYPE html>
<html>
	<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		.div_config {
			border-top:1px solid gray;
			width:800px;
			margin:15px auto;
			font-family:sans-serif;
			padding:5px;
		}
		h1, h2, .error {
			text-align:center;
		}
		p {
			color:gray;
			margin-bottom:0;
		}
		p.error {
			color:red;
		}
		input[type="text"], input[type="email"], input[type="number"], input[type="url"] {
			width:800px;
		}
	</style>
	</head>
	<body>
		$html
	</body>
</html>
EOT;
}
?>
