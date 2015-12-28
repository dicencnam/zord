/**
 * @package Liseuse
 * @author David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

window.$func = {};
window._Msg = null;
window.loadEpub = null;

(function() {
	window.__loadNextFile = null;

		$bundles('appli', 'Liseuse', function(Liseuse) {

			// initalisations
			Liseuse.init(CONFIG);
			loadEpub = Liseuse.loadEpub;

			var __loadBootstrap = function ( list, fnc ) {

				var tmp = function (list, fnc){
					var __length = list.length-1;
					var __numFile = -1;
					return function (){
						if(__length!=__numFile){
							__numFile++;
							var __file = list[__numFile];
							var __name = __file.split(/\//).pop();
							__name = __name.substr(0,__name.length-7);
							$req(__file, function () {
								//__msg(__name, true);
							});
						} else {
							fnc();
						}
					};
				}
				__loadNextFile = tmp(list, fnc);
				__loadNextFile();
			};

			__loadBootstrap(BOOTSTRAP_BUNDLES,function(){
				delete __loadNextFile;
			});
		});

}());
