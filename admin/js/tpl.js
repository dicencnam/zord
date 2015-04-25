/**
* Moteur de template
* @class $tpl
* @static
* @module JSElem
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
window.$tpl = (function(undefined) {

	var er_template_g = /\{\{template([^\}]*)\}\}([\s\S]*?)\{\{\/template\}\}/g;
	var er_template_s = /\{\{template([^\}]*)\}\}([\s\S]*?)\{\{\/template\}\}/;

	var er_key = /\{\{\$([\w\.]*)\}\}/g;

	var _getTpl = function ( script ) {
		var s = script.match(er_template_s);
		var attributes = (s[1].trim()).split(' ');
		var tabTPL = {
			content : s[2]
		};
		attributes.forEach(function(attrs) {
			var vk = attrs.split('=');
			if(vk.length>1)
				tabTPL[vk[0]] = vk[1].substr(1,vk[1].length-2);
		});
		return tabTPL;
	};

	return {

		//(inspiration : https://github.com/trix/nano)
		/**
		* Create render
		* @method render
		* @param {string} template Templates(s)
		* @param {object} data Data to replace
		* @return {string}
		*/
		render : function (template, data) {
			return template.replace(er_key, function (str, key) {
				var keys = key.split(".");
				var value = data[keys.shift()];
				try {
					keys.forEach( function (val) {value = value[val]; });
						return (value === null || value === undefined) ? "" : value;
				} catch(err) {
					return "";
				}
			});
		},

		/**
		* Load template(s)
		* @example

			file.tpl :

			<!-- beforebegin -<p>- afterbegin -foo- beforeend -</p>- afterend -->
			{{template id="xx1" overlay="overlayID1" insert="afterend" preload="true"}}
				<option value="{{$value}}">{{$label}}</option>
			{{/template}}

			{{template id="xx2" overlay="overlayID2" insert="beforeend" preload="false"}}
			...
			{{/template}}
			...

		* @method loadTemplate
		* @param {string} templates Templates
		* @return {object}
		*/
		loadTemplate : function ( templates ) {
			var tpls = {};
			var tab = templates.match(er_template_g);
			if(tab){
				tab.forEach(function(_script,index) {
					var _tpl = _getTpl(_script);
					if(_tpl.id==undefined)
						_tpl.id = index;
					tpls[_tpl.id] = _tpl;
				});
				return tpls;
			}
			return null;
		}
	};
})();
