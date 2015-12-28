<?php

$configs = array(
	'db_user' => array(
		'type' => 'orm',
		'definition' => array(
			 'DB_USER_ENGINE' => array(
					'comment'=> 'Engine',
					'type' => 'string',
					'value' => 'mysql'
			 ),
			 'DB_USER_HOST' => array(
					'comment'=> 'Host',
					'type' => 'string',
					'value' => 'localhost'
			 ),
			 'DB_USER_DBNAME' => array(
					'comment'=> 'DB name',
					'type' => 'string',
					'value' => 'zord'
			 ),
			 'DB_USER_ID' => array(
					'comment'=> 'ID don\'t change !',
					'type' => 'hidden',
					'value' => 'zord_user'
			 ),
			 'DB_USER_USERNAME' => array(
					'comment'=> 'User name',
					'type' => 'string',
					'value' => ''
			 ),
			 'DB_USER_PASSWORD' => array(
					'comment'=> 'Password',
					'type' => 'string',
					'value' => ''
			 )
		)
	),
	'db_admin' => array(
		'type' => 'orm',
		'definition' => array(
			 'DB_ADMIN_ENGINE' => array(
					'comment'=> 'Engine',
					'type' => 'string',
					'value' => 'mysql'
			 ),
			 'DB_ADMIN_HOST' => array(
					'comment'=> 'Host',
					'type' => 'string',
					'value' => 'localhost'
			 ),
			 'DB_ADMIN_DBNAME' => array(
					'comment'=> 'DB name',
					'type' => 'string',
					'value' => 'zord'
			 ),
			 'DB_ADMIN_ID' => array(
					'comment'=> 'ID don\'t change !',
					'type' => 'hidden',
					'value' => 'zord_admin'
			 ),
			 'DB_ADMIN_USERNAME' => array(
					'comment'=> 'User name',
					'type' => 'string',
					'value' => ''
			 ),
			 'DB_ADMIN_PASSWORD' => array(
					'comment'=> 'Password',
					'type' => 'string',
					'value' => ''
			 )
		)
	),

	'solr' => array(
		'type' => 'define',
		'definition' => array(
			'SOLR_SERVER_HOSTNAME' => array(
				 'comment'=> 'Domain name of the Solr server',
				 'type' => 'string',
				 'value' => 'localhost'
			),
			'SOLR_SECURE' => array(
				 'comment'=> 'Whether or not to run in secure mode',
				 'type' => 'hidden_boolean',
				 'value' => 'false'
			),
			'SOLR_SERVER_PORT' => array(
				 'comment'=> 'HTTP Port to connection',
				 'type' => 'integer',
				 'value' => 8983
			),
			'SOLR_SERVER_PATH' => array(
				 'comment'=> 'Solr core path',
				 'type' => 'string',
				 'value' => '/solr/zord'
			)
		)
	),

	'admin' => array(
		'type' => 'define',
		'definition' => array(
			'ADMIN_MODULE_DEFAULT' => array(
				 'comment'=> 'Module adminstration default',
				 'type' => 'hidden',
				 'value' => 'Admin',
				 'rename' => 'MODULE_DEFAULT'
			),
			'ADMIN_ACTION_DEFAULT' => array(
				 'comment'=> 'Action adminstration default',
				 'type' => 'hidden',
				 'value' => 'connexion',
				 'rename' => 'ACTION_DEFAULT'
			),
			'ADMIN_PROJECT_FOLDER' => array(
				 'comment'=> 'Project folder',
				 'type' => 'string',
				 'value' => '/',
				 'rename' => 'PROJECT_FOLDER'
			)
		)
	),

	'user' => array(
		'type' => 'define',
		'definition' => array(
			'USER_MODULE_DEFAULT' => array(
				 'comment'=> 'Module user default',
				 'type' => 'hidden',
				 'value' => 'Start',
				 'rename' => 'MODULE_DEFAULT'
			),
			'USER_ACTION_DEFAULT' => array(
				 'comment'=> 'Action user default',
				 'type' => 'hidden',
				 'value' => 'index',
				 'rename' => 'ACTION_DEFAULT'
			),
			'USER_PROJECT_FOLDER' => array(
				 'comment'=> 'Project folder',
				 'type' => 'string',
				 'value' => '/',
				 'rename' => 'PROJECT_FOLDER'
			)
		)
	),

	'appli' => array(
		'type' => 'define',
		'definition' => array(
			'APPLI_DEBUG' => array(
				 'comment'=> 'Debugging',
				 'type' => 'boolean',
				 'value' => true,
				 'rename' => 'DEBUG'
			),
			'APPLI_SALT' => array(
				 'comment'=> 'Salt',
				 'type' => 'string',
				 'value' => 'hjRE$!670gfK',
				 'rename' => 'SALT'
			),
			'APPLI_TEICSSNAMEFILE' => array(
				 'comment'=> 'Name of TEI CSS file',
				 'type' => 'string',
				 'value' => 'Klkj_sCFvg_781',
				 'rename' => 'TEICSSNAMEFILE'
			),
			'APPLI_CONTROLER' => array(
				 'comment'=> 'Controler',
				 'type' => 'hidden',
				 'value' => 'SimpleControler',
				 'rename' => 'CONTROLER'
			),
			'APPLI_REQUEST' => array(
				 'comment'=> 'Request',
				 'type' => 'hidden',
				 'value' => 'SimpleUrl',
				 'rename' => 'REQUEST'
			),
			'APPLI_LANG_DEFAULT' => array(
				 'comment'=> 'Lang default',
				 'type' => 'string',
				 'value' => 'fr-FR',
				 'rename' => 'LANG_DEFAULT'
			),
			'APPLI_OPENURL' => array(
				 'comment'=> 'OPENURL',
				 'type' => 'string',
				 'value' => 'http://xxxxx/openurl/?id=',
				 'rename' => 'OPENURL'
			),
			'APPLI_OBFUSCATION_MODELS_MAX' => array(
				 'comment'=> 'Obfusction models maximum',
				 'type' => 'integer',
				 'value' => 45,
				 'rename' => 'OBFUSCATION_MODELS_MAX'
			),
		)
	)
);
?>
