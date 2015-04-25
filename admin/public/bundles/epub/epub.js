/**
 * Bundle epub
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
define('bdl/epub/epub',function() {
	var getEpubs = function(){
		MSG.wait();
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'getEpubs'
			},
			success : function (data) {

				for(var portal in data.books){
					$bundles.tpls.epub_portal({portal:portal});
					for(var isbn in data.books[portal]){
						var level = data.books[portal][isbn].level*1;
						var cl = '';
						if(level>0)
							cl = ' class="draft"';
						$bundles.tpls.epub_item({
							title:data.books[portal][isbn].title,
							isbn:isbn,
							portal:portal,
							level:cl
						});
					}
				}
				MSG.hide();
			}
		});
	};
	var createEpubs = function(books){
		MSG.wait();
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'createEpubs',
				books : JSON.stringify(books)
			},
			success : function (data) {
				for(var portal in books){
					books[portal].forEach(function(isbn){
						var element = document.getElementById('epubs_table').querySelector('input[value="'+isbn+'"]').parentNode.parentNode;
						element.parentNode.removeChild(element);
					});
				}
				MSG.hide();
			}
		});
	};
	var epubView = {
		init : function () {
			getEpubs();

			document.getElementById('epub_allselect').addEventListener('click', function(event){
				[].forEach.call(document.getElementById('epubs_table').querySelectorAll('input'), function (el) {
					el.checked = true;
				});
			});
			document.getElementById('epub_allunselect').addEventListener('click', function(event){
				[].forEach.call(document.getElementById('epubs_table').querySelectorAll('input'), function (el) {
					el.checked = false;
				});
			});
			document.getElementById('epub_create').addEventListener('click', function(event){
				var epubs = {};
				var create = false;
				[].forEach.call(document.getElementById('epubs_table').querySelectorAll('input'), function (el) {
					if(el.checked){
						create = true;
						var portal = el.getAttribute('data-portal');
						if(epubs[portal]==undefined)
							epubs[portal] = [];
						epubs[portal].push(el.value);
					}
				});
				if(create)
					createEpubs(epubs);
			});
		}
	};
	return epubView;
});
