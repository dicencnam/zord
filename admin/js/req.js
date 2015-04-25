

// plugins path
if(undefined===$pluginPath)
	var $pluginPath = 'library/plugin/';

// charged targets
var $definition = {
	__queue__ : {},
	get : function(name){
		return $definition[name];
	}
};


/**
* Module définition
*
* @param {String} moduleName Module name
* @param {Mix} requireFilesOrCbk  Dependence file(s) or callback
* @param {Function} callback Callback function
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
window.define = (function(undefined) {
	var define = function (moduleName, requireFilesOrCbk, callback) {
		if ( typeof requireFilesOrCbk == 'function')
			$definition[moduleName] = requireFilesOrCbk();
		else {
			$definition[moduleName] = '__wait__';
			$req(requireFilesOrCbk,function(){;
				$definition[moduleName] = callback.apply(null,arguments);
			});
		}
	};

	define.amd = {
		'plugins': true
	};

	return define;
}());

/**
* Autoloader

	$req(['fileX','tpl!templatex'],function(objx,tplx){
		...
	});

	// define file
	define('path/file',function() {
		 return {
			...
		};
	});

* @class $req
* @static
* @module JSElem
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
(function(undefined) {

	var config = {
		locale : 'fr-FR',
		UIType : 'html',
		DS : '/',
		platform_win : false,
		winReg : null,
		baseUrl : '',
		paths : {}
	};

	// plugin JS
	define($pluginPath+'js',function() {
		var req_plugin_js = {
			load : function ( map, require, callback, config ) {
				var c = function(){
					if(undefined===$definition[map.id])
						callback(null);
					if(undefined!==map.plugOptions['callback'])
						map.plugOptions['callback']();
				};

				var sc = document.createElement("script");
				sc.setAttribute("type", "text/javascript");
				sc.setAttribute("src", map.url);

				if (sc.addEventListener)
					sc.addEventListener("load",function () {
						c();
					}, false);
				else if(sc.readyState)
					sc.onreadystatechange = function () {
						c();
					};
				document.getElementsByTagName('head')[0].appendChild(sc);
			}
		};
		return req_plugin_js;
	});

	// plugin xhr
	define($pluginPath+'xhr',function() {

		var req_plugin_xhr = {
			load : function ( map, require, callback, config ) {
				// fichier par défaut
				if(map.ext=='.js') {
					map.ext = '.html';
					map.url = map.url+map.ext;
				}
				var xhr = new XMLHttpRequest(),
					async = false;
				if(map.plugOptions['async']!==undefined && map.plugOptions['async']==='true')
					async = true;
				xhr.open("GET", map.url, async);

				if(xhr.overrideMimeType)
					xhr.overrideMimeType("text/plain");

				xhr.onreadystatechange = function () {
					if (xhr.readyState == 4) {
						if (xhr.status == 200) {
							callback(xhr.responseText);
						} else {
							console.log('Error load file:'+map.url);
							callback(null);
						}
					}
				};
				xhr.send();
			}
		};

		return req_plugin_xhr;
	});

	// carte de la ressource (id, plugin, extension, options, url)
	var makeMap = function ( target ) {

		var map = {
			realName : target,
			id : '',
			plug : 'js',
			plugOptions : {},
			ext : '.js',
			url : ''
		};

		// plugin
		var plug = '';
		var subPlug = target.indexOf("!");

		if (subPlug>-1) {
			// nom du plugin
			map.plug = target.substr(0,subPlug);
			target = target.substr(subPlug+1);
			// options du plugin
			var _plugOptions = target.split('!');
			target = _plugOptions[0];
			_plugOptions.shift();
			_plugOptions.forEach(function(option) {
				option = option.split(':');
				map.plugOptions[option[0]] = option[1];

			});
			// id de la ressource
			map.id = map.plug+'!'+target;
		}

		// si l'id n'a pas été défini (pas de plugin) on le défini
		if(map.id=='')
			map.id = target;

		// extension
		var subExt = target.lastIndexOf(".");
		if (subExt>-1)
			map.ext = target.substr(subExt+1);

		// path
		var sub = target.indexOf("/"),
			name = target.substr(0,sub);
		if(config.paths[name])
			map.url = config.baseUrl+config.paths[name]+target.substr(sub);
		else
			map.url = config.baseUrl+target;

		map.noext = map.url;
		// extension par défaut pour les fichiers js
		if (map.ext=='.js' && subExt==-1 && map.plug=='js')
			map.url += map.ext;

		return map;
	};

	// callback après le chargement
	var afterLoad = function (resourceID) {
		return function(data) {
			if(undefined === data)
				data = null;
			$definition[resourceID] = data;
		};
	};

	// chargement de ressources
	var load = function ( pl, deps ) {
		deps.forEach(function(map) {
			$definition[pl].load.apply(this,[ map, $req, afterLoad(map.id), config]);
		});
	};

	// dépendance à un plugin non chargé
	var loadDeps = function ( pl, deps ) {
		var map = makeMap(pl);
		map.plugOptions['callback'] = function(){load(pl,deps);};
		$definition[$pluginPath+'js'].load.apply(this,[ map, $req, afterLoad(map.id), config]);
	};

	// "domReady!" quand le dom est ready
	var domready = (function(){
		var load_events = [];
		var _init = function () {
			load_events.forEach(function(fn) { fn();});
		};

		if (document.addEventListener)
			document.addEventListener("DOMContentLoaded", _init, false);
		else
			window.onload = function() {_init();};

		return function (fn) {
			if (document.readyState == "complete") fn(); else load_events.push(fn);
		};
	})();

	var $req = function() {

		// au moins un argument
		if (0 === arguments.length || undefined === arguments[0])
			return;

		// si il n'y a qu'une adresse on la range dans un tableau
		if (typeof arguments[0] === 'string')
			arguments[0] = [arguments[0]];

		// variables
		var callback = arguments[1] || null,
			goodCallback = null, ids = [], deps = {}, noDeps = {};

		// on scanne les sources
		// les sources non chargés ou qui dépendent de plugins
		// sont collectées
		arguments[0].forEach(function(id) {

			// construction de carte de la ressource
			var map = makeMap(id);

			// construction du callback pour un domReady
			if(map.plug=='domReady') {
				goodCallback = domready(callback);
			} else {
				// plugin
				var plug = $pluginPath+map.plug;

				// listes des ressources
				ids.push(map.id);

				// création de 2 listes
				// ressources dépendantes de plugins non chargés
				// et ressources non chargés
				if (undefined === $definition[plug]) { // plugin non chargé donc ressource non chargé
					if(undefined === deps[plug])
						deps[plug] = [];

					if($definition.__queue__[map.id]==undefined) {
						$definition.__queue__[map.id] = true;
						deps[plug].push(map);
					}
				} else {
					if($definition[map.id]==undefined && $definition.__queue__[map.id]==undefined) {// ressource non chargé
						if(noDeps[plug]==undefined)
							noDeps[plug] = [];
						$definition.__queue__[map.id] = true;
						noDeps[plug].push(map);
					}
				}
			}
		});
		// si domReady! n'est pas dans la liste des ressources
		// on assigne le bon callback
		if(goodCallback==null)
			goodCallback = callback;

		// on charge toutes les ressources non chargé
		// qui dépendent de plugins non chargés
		for (var key in deps)
			loadDeps(key,deps[key]);

		// qui ne dépendent pas de plugins non chargés
		for (var key in noDeps)
			load(key,noDeps[key]);

		// ----------------------------------------------------------------------------

		// fonction de test que le chargement de ressources est effectué
		// pour lancer la fonction de callback si elle existe
		var testLoad = function ( scripts, delay, _callback ) {

			var scriptDelay = {};
			scripts.forEach(function(sc) {
				scriptDelay[sc] = delay;
			});

			var scriptsLoadTMP = scripts.slice(0);

			var scriptRemove = function (sc) {
				for (var i = 0; i < scriptsLoadTMP.length; i++)
					if (scriptsLoadTMP[i] === sc)
						scriptsLoadTMP.splice(i,1);
			};

			return function() {
				if (scriptsLoadTMP.length==0) {
					if (_callback !== null) {
						var _args = [];
						scripts.forEach(function(script) {
							if($definition[script]!=null)
								_args.push($definition[script]);
						});
						_callback.apply(null, _args);
					}
				} else {
					scriptsLoadTMP.forEach(function(script) {
						// est-ce que le script est chargé
						if(undefined!==$definition[script]) {
							// oui mais pas encore les dépendances
							if ($definition[script]!='__wait__')
								scriptRemove(script);// on supprime le script de la liste
						} else {
							if (scriptDelay[script]==0) {
								console.log('Timeout:' + script);
								scriptRemove(script);
							} else {
								if (undefined===$definition[script])
									scriptDelay[script]--;
							}
						}
					});
					window.setTimeout(testLoad_C,100);
				}
			};
		};
		// création de la closure de test
		// arguments : liste des ressources, délai d'attente (6s), callback
		var testLoad_C = testLoad(ids, 60, goodCallback );

		window.setTimeout(testLoad_C,10);

		return $req;
	};

	// ------------------------------------------------------------------

	/**
	* Config
	* @example
	// Autoloader config
		$req.config({
			locale : document.documentElement.lang,
			UIType : 'html',
			DS : '/', // directory separator
			platform_win : false,
			baseUrl : PATH,
			paths : { // shortcut
				"cp" : "js/components",
				"library" : "js",
				"public" : "",
				"bdl" : "public/bundles",
			}
		});
	* @method config
	* @param {Object} _config
	* @return {Object} $req class
	*/
	$req.config = function ( _config ) {
		for (var key in _config)
			config[key] = _config[key];
		return $req;
	};

	/**
	* Chaining à new require
	* @method next
	* @return {object} $req class
	*/
	$req.next = function ( ) {
		$req.apply(this, arguments);
		return $req;
	};

	/**
	* Get a map
	* @method getMap
	* @param {string} target Adresse file
	* @return {object} $req class
	*/
	$req.getMap = function ( target ) {
		return makeMap(target);
	};

	return (window.require = window.$req = $req);
}());
