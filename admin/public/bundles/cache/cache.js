/**
 * Bundle cache
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
define('bdl/cache/cache',function() {

	var _emptyCache = function(){
		MSG.wait();
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'emptyCache'
			},
			success : function (data) {
				MSG.hide();
			}
		});
	};

	var cacheView = {
		init : function () {
			document.getElementById('cache_send').addEventListener('click', function(event){
				MSG.confirm(
					$bundles.tpls['cache_confirm_title'],
					$bundles.tpls['cache_confirm_content'],
					function(save){
						if(save)
							_emptyCache();
					}
				);
			});
		}
	};
	return cacheView;
});
