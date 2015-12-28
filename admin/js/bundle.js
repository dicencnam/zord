/**
* bundles loader

	// load bundle
	$bundles('appli', 'appli', function(appliObj) {
		appliObj.init();
	});

* @class $bundles
* @static
* @module JSElem
* @author David Dauvergne
* @copyright 2014 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

/**
 * bundles loader

exemple template preload

{{template id="myAccount_connect" overlay="column_center" insert="inner" preload="true"}}
<div id="myAccount_connect">{{$connexion}}</div>
{{/template}}


// le template est inséré selon l'attribut "insert" sur l'overlay avec les locales
$bundles.tpls.myAccount_connect();

*/
window.$bundles = (function(undefined) {
	var _push = function ( filesLoad, element) {
		if (element!==undefined){
			var _count = 1;
			if(typeof element!=='string')
				element.forEach(function(item,i){filesLoad.push(item);_count = i+1;});
			else
				filesLoad.push(element);
			return _count;
		} else {
			return 0;
		}
	};
	var _load = function ( name, bundle, callback) {
		var filesLoad = [];
		// tpl
		var _tplCount = _push(filesLoad,bundle.tpl);
		// les locales
		var _localeCount = _push(filesLoad,bundle.locale);
		// css
		var _cssCount = _push(filesLoad,bundle.css);

		var initbundle = function () {
			// lancement de la function ou chargement du script d'initialisation
			if (bundle.init!==undefined) {
				if (typeof bundle.init =='function')
					bundle.init(callback);
				else
					$req(bundle.init,callback);
			} else {
				if (callback!==undefined)
					callback();
			}
		};

		var loadbundle = function (filesLoad) {
			var _preload = function(tpl,render){
				return function(data){
					if(data!==undefined)
						render = $tpl.render(tpl.content,data);
					document.getElementById(tpl.overlay).insertComponent(tpl.insert,render);
				};
			};
			$req(filesLoad,
				function () {
					var args = Array.prototype.slice.call(arguments);
					var posArg = -1;
					var _merge = function(_count){
						var _x = undefined;
						if(_count>1){
							_x = {};
							for (var i = 0; i < _count; i++) {
								posArg++;
								for (var key in args[posArg])
									_x[key] = args[posArg][key];
							}
						} else if(_count===1){
							posArg++;
							_x = args[posArg];
						}
						return _x;
					};
					var tpls = _merge(_tplCount);
					var lang = _merge(_localeCount);
					var tplRender = '';

					if (tpls) {
						for (var t in tpls) {
							// on passe par un moteur de template JS si la langue existe
							if (tpls[t] && lang && tpls[t].lang===undefined)
								tplRender = $tpl.render(tpls[t].content,lang);
							else
								tplRender = tpls[t].content;

							if (tpls[t].overlay!==undefined){
								if(tpls[t].preload===undefined)
									document.getElementById(tpls[t].overlay).insertComponent(tpls[t].insert,tplRender);
								else
									$bundles.tpls[t] = _preload(tpls[t],tplRender);
							} else {
								$bundles.tpls[t] = tplRender;
							}
						}
					}
					initbundle();
				}
			);
		};

		// insertion des namespace pour les composants
		if (bundle.componentsNamespace!==undefined) {
			for (var namespace in bundle.componentsNamespace)
				JSElem.namespace.add(namespace,bundle.componentsNamespace[namespace]);
		}
		if (bundle.components!==undefined) {
			// chargement des composants pour l'appli
			var allComponents = [];
			for (var cp in bundle.components)
				bundle.components[cp].forEach(function(i){allComponents.push(i)});
			$req(allComponents,function(){
				loadbundle(filesLoad);
			});
		} else {
			loadbundle(filesLoad);
		}
	};

	var config = {
		bundlesFiles : {}
	};

	var register = {};

	var bundles = function ( category, name, callback ) {
		if(config.bundlesFiles[category]) {
			$req(config.bundlesFiles[category], function (ex) {
				if(ex){
					if(ex[name])
						_load(name,ex[name],callback);
					else
						console.log('Name of the bundle does not exist:'+name);
				}
			});
		} else {
			console.log('Class of the bundle does not exist:'+category);
		}
	};

	/**
	* Bundles configuration files

		_config : {
			appli: "bdl/admin/admin_bdl"
		};

		// file admin_bdl.js

		define('bdl/admin/admin_bdl',function() {
			var bundles = {
				'appli' : {
					components : { // list of components required for this bundle
						stringbundle : ['cp/stringbundle/cp_stringbundle'],
						popupboxes : ['cp/popupboxes/cp_popupboxes','css!cp/popupboxes/cp_popupboxes']
					},
					tpl : 'tpl!bdl/admin/admin',// template
					locale : 'i18n!bdl/admin/locale/admin!default:fr-FR',// loacale
					init : ['bdl/admin/admin'], // JS
					css : 'css!bdl/appli/appli'// CSS
				}
			};
			return bundles;
		});

	*
	* @method config
	* @param {object} _config
	*/
	bundles.config = function ( _config ) {
		for (var key in _config)
			config[key] = _config[key];
	};

	/**
	* Register a specific bundle
	* @method register
	* @param {string} category Category of bundle
	* @param {string} name Name of bundle
	* @param {object} data Configuration of bundle
	*/
	bundles.register = function ( category, name, data ) {
		if(register[category]===undefined)
			register[category] = {};
		register[category][name] = {data:data,l:false};
	};

	/**
	* Load a specific bundle
	* @method load
	* @param {string} category Category of bundle
	* @param {string} name Name of bundle
	*/
	bundles.load = function ( category, name ) {
		if(register[category]!==undefined && register[category][name]!==undefined) {
			if(!register[category][name].data.l) {
				$bundles(category, name, register[category][name].data.load);
				register[category][name].data.l = true;
			}
		}
	};

	/**
	* Unload a specific bundle
	* @method unload
	* @param {string} category Category of bundle
	* @param {string} name Name of bundle
	*/
	bundles.unload = function ( category, name ) {
		if(register[category]!==undefined && register[category][name]!==undefined) {
			if(register[category][name].data.l) {
				$req(config.bundlesFiles[category], function (ex) {
					$req(ex[name].init,register[category][name].data.unload);
				});
				register[category][name].data.l = false;
			}
		}
	};

	bundles.tpls = {};

	return bundles;
})();
