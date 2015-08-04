/**
 * @package  Component
 * @subpackage component (cp_)
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

/**
 * Composants tabbox
 * namespace : http://www.components.org
 */
define('cp/tabbox/cp_tabbox',function() {
	(function() {

		var tabbox = {
			methods : {
				domInsert : function () {
					var _this = this;
					setTimeout(function(){
						var x = _this.getAttribute('selectedindex');
						if(x!=undefined)
							_this.setAttribute('selectedindex',x);
					},200);
				},
				getIndexById : function (value) {
					var x = 0, r = null;
					[].forEach.call(this.querySelectorAll('cp\\:tabs'), function (el) {
						if(el.getAttribute('id')==value)
							r = x;
						x++;
					});
					return r;
				}
			},

			attributes : {
				selectedindex : {
					set : function (value) {
						var tabs = this.querySelectorAll(':scope > cp\\:tabs')[0];

						var _tabs = [];
						var queryTabs = tabs.querySelectorAll(':scope > cp\\:tab');
							for (var i = 0; i < queryTabs.length; i++)
								_tabs.push(queryTabs[i]);
						_tabs.forEach(function(el,i){
							if(i == value)
								el.setAttribute('selected', true);
							else
								el.removeAttribute('selected');
						});
						var tabpanels = this.querySelectorAll(':scope > cp\\:tabpanels')[0];
						setTimeout(function(){
							[].forEach.call(tabpanels.querySelectorAll(':scope > cp\\:tabpanel'),function(el, i){
								if(i == value)
									el.setAttribute('selected', true);
								else
									el.removeAttribute('selected');
							});
						}, 10);
					}
				}
			}
		};
		JSElem.register('http://www.components.org','tabbox',tabbox);

		/* -------------------  tabs  ------------------- */
		var tabs = {};
		JSElem.register('http://www.components.org','tabs',tabs);

		/* -------------------  tab  ------------------- */
		var tab = {
			attributes : {
				selected : {}
			},
			events : {
				click : function (event) {
					//event.stopPropagation();
					this.focus();
					var tabs = this.parentNode;
					var _tabs = [];
					var queryTabs = tabs.querySelectorAll('cp\\:tab');
					for (var i = 0; i < queryTabs.length; i++)
						_tabs.push(queryTabs[i]);

					tabs.parentNode.setAttribute('selectedindex',_tabs.indexOf(this));
				}
			}
		};
		JSElem.register('http://www.components.org','tab',tab);

		/* -------------------  tabpanels  ------------------- */
		var tabpanels = {};
		JSElem.register('http://www.components.org','tabpanels',tabpanels);

		/* -------------------  tabpanel  ------------------- */
		var tabpanel = {
			attributes : {
				selected : {},
				innerstyle : {
					get : function () {
						return this.getAnonid('tabpanel').getAttribute('style');
					},
					set : function (value) {
						this.getAnonid('tabpanel').setAttribute('style',value);
					}
				}
			}
		};
		JSElem.register('http://www.components.org','tabpanel',tabpanel);
	}());
});
