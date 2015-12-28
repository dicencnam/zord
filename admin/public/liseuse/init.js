
// config liseuse
var _liseuseConfig = {
	// Nom du epub (dossier)
	epub : undefined,
	// path du dossier des epubs
	epubsPath : 'epubs/',
	// path de la liseuse
	liseusePath : '../public/liseuse/',
	// epub par défaut
	epubNotFound : 'notFound',
	// bundles list
	bundlesList : "../public/liseuse/bundlesList.js",
	// chargement automatique
	autoload : true,
	// debug
	debug : false,

	options : {}
	// dézippé ou non
	// dossier avec le epub décompressé ou le fichier epub
	// uncompress : true,
};


if(typeof CONFIG == 'undefined')
	CONFIG = {};

for(var key in _liseuseConfig){
	if(typeof CONFIG[key]=='undefined')
		CONFIG[key] = _liseuseConfig[key];
}

// version des navigateurs testés
var browserVersion = {
	Chrome : 24,
	Firefox : 18,
	Explorer : 10,
	Trident : 11,
	Safari : 6,
	Opera : 12.16,
	Android : 34,
	CriOS : 30 // chrome ipad ios7
};

// type d'interface
var UITYPE = {
	// root de l"appli
	// html → "../" ou ""
	baseUrl : CONFIG.liseusePath,
	paths : {
		"cp" : "../js/components",
		"js" : "../js",
		"library" : "../js",
		"bdl" : "../public/bundles",
	}
};

/* --------------------------------------------------------- */
var BOOTSTRAP_BUNDLES = [];

var BUNDLESFILES = {
	appli:{type:"appli",file:CONFIG.bundlesList}
};

var BOOTSTRAP = "../public/liseuse/bootstrap.js";

var initFiles = [
		'js/notify', // pattern de notification
		'js/tpl', // moteur de template simple
		'js/bundle', // loader de bundle
		'js/JSElem' // gestionnaire de composant
	];

var FILEPROTOCOL = false;
var FILEURL = '';
/* --------------------------------------------------------- */

(function() {

	// lecture par file://
	if(window.location.protocol=='file:'){
		FILEPROTOCOL = true;
		var _url = window.location.pathname.split('/');
		_url.pop();
		FILEURL = _url.join('/')+'/';
	}

	// test du navigateur
	var browserTest = function(browser) {
		if(browserVersion[browser.name]!=undefined){
			if (browser.version >= browserVersion[browser.name]){
				return true;
			} else {
				alert('Your browser is too old');
				return false;
			}
		} else {
			alert('Untested browser. The application may not work optimally.');
			return true;
		}
	};

	if(browserTest({name: BrowserDetect.browser,version: BrowserDetect.version})) {

		// ------------------------------------------------------------------
		// ------------------------------------------------------------------

		/**
		*
		* Log
		*
		* @param {string} msg Message
		*/
		window.$log = function (msg){
			if($params.debugMode && typeof console != 'undefined') {
				if(console.debug) // FireBug
					console.debug(msg);
				else
					console.log(msg);// autres
			}
		};

		window.$params = (function() {

			var _params = {
				// debuggage
				debugMode : CONFIG.debug,

				// la langue
				lang : document.documentElement.lang,

				UIType : 'html',
				DS : '/',
				platform_win : false,
				winReg : null
			};

			_params['baseUrl'] = UITYPE.baseUrl;
			_params['paths'] = UITYPE.paths;
			// au cas ou des "console.log(...)" se trouvent encore dans le code
			// et éviter un plantage si "console" est undefined
			window.console = window.console || {log: function() {}};

			return _params;
		}());

		/* -------------------------------------------------------------- */
		// configuration de l'autoloader
		$req.config({
			locale : $params.lang,
			UIType : $params.UIType,
			DS : $params.DS,
			platform_win : $params.platform_win,
			winReg : $params.winReg,
			baseUrl : $params.baseUrl,
			paths : $params.paths
		});

		/* -------------------------------------------------------------- */

		// chargement des modules définit dans l'initialisation
		$req(initFiles,function(){
			// configuration $bundles
			var _bundlesFiles = {};
			for(var bundle in BUNDLESFILES) {
				_bundlesFiles[bundle] = BUNDLESFILES[bundle].file;
			}
			$bundles.config({bundlesFiles : _bundlesFiles});
			JSElem.DOMContentLoaded(function(){
				// bootstrap
				$req(BOOTSTRAP);
			});
		});
	}
})();
