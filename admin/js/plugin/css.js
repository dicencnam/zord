/**
 * Plugin CSS $req
 * @author David Dauvergne
 * @copyright 2013 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
define($pluginPath+'css',function() {

	var req_plugin_css = {
		load : function ( map, require, callback, config ) {
			// default file
			if(map.ext=='.js') {
				map.ext = '.css';
				map.url = map.url+map.ext;
			}

			var c = function(){
				if($definition[map.id]==undefined)
					callback(null);
			};

			var cssElem =document.createElement("link");
			cssElem.setAttribute("rel", "stylesheet");
			cssElem.setAttribute("type", "text/css");
			cssElem.setAttribute("href", map.url);
			document.getElementsByTagName('head')[0].appendChild(cssElem);
			c(null);
		}
	};

	return req_plugin_css;
});
