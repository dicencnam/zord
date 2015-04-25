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
			document.addEventListener('scroll', function(){
				var tocTEI = document.getElementById('tocTEI');
				if(tocTEI!=undefined){
					window.scrollY >= window.origOffsetY ? tocTEI.classList.add('tocTEIFix') :
					tocTEI.classList.remove('tocTEIFix');
				}
			});
		}
	};
	return adminView;
});
