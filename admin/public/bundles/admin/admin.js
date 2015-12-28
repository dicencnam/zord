/**
 * Bundle books
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
define('bdl/admin/admin',function() {

	var adminView = {
		init : function () {
			// récupération d'une locale
			window.STRBUNDLE = document.getElementById('strbundles');
			// élément des messages
			window.MSG = document.getElementById('msg');
			// panels
			window.$panelsTabs = function(element){
				[].forEach.call(element.querySelectorAll('.tabs'), function (el) {
					[].forEach.call(el.querySelectorAll('div[data-tab]'), function (tab,i) {
						if(i==0)
							tab.classList.add('tabselect');
						tab.addEventListener("click", function(event) {
							var v = this.getAttribute('data-tab');
							[].forEach.call(element.querySelectorAll('div[data-tab]'), function (_tab) {
								_tab.classList.remove('tabselect');
							});
							[].forEach.call(element.querySelectorAll('div[data-panel]'), function (panel) {
								panel.classList.remove('paneselect');
								if(panel.getAttribute('data-panel')==v)
									panel.classList.add('paneselect');
							});
							this.classList.add('tabselect');
						});
					});
					[].forEach.call(element.querySelectorAll('div[data-panel]'), function (panel,i) {
						if(i==0)
							panel.classList.add('paneselect');
					});
				});
			};

			var services = ['portal','source','publication','cache','users','counter','epub'];

			services.forEach(function(service){
				document.getElementById('admin_'+service).addEventListener("click", function(){
					MSG.wait();
					$bundles('appli', service, function(obj) {
						obj.init();
						MSG.hide();
					});
				}, false);
			});
			WEBSITES.forEach(function(site){
				$req('library/TEI_'+site);
			});
		}
	};
	return adminView;
});
