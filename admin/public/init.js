/**
 * Init
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
(function() {

	// Browser version
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

	var initFiles = [
		'public/bundle_list',
		'library/notify',
		'library/tpl',
		'library/bundle',
		'library/ajax',
		'library/JSElem'
	];

	// Browser test
	var browserTest = function(browser) {
		if(browserVersion[browser.name]!=undefined){
			if (browser.version >= browserVersion[browser.name]){
				return true;
			}
			alert('Your browser is too old');
			return false;
		}
		alert('Untested browser. The application may not work optimally.');
		return true;
	};

	if(browserTest({name: BrowserDetect.browser,version: BrowserDetect.version})) {

		// Autoloader config
		$req.config({
			locale : document.documentElement.lang,
			UIType : 'html',
			DS : '/',
			platform_win : false,
			winReg : null,
			baseUrl : PATH,
			paths : {
				"cp" : "js/components",
				"library" : "js",
				"public" : "",
				"bdl" : "public/bundles",
			}
		});

		/* -------------------------------------------------------------- */

		// Initialisation
		$req(initFiles,function(bundlesFiles){
			// Bundles configuration
			$bundles.config({bundlesFiles : bundlesFiles});
			JSElem.DOMContentLoaded(function(){
				$bundles('appli', 'appli', function(appliObj) {
					appliObj.init();
				});
			});
		});
	}
})();
