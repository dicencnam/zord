/**
 * Bundle publication
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
define('bdl/publication/publication',function() {
	var locale = null;
	var getDocs = function(){
		MSG.wait();
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'getDocs'
			},
			success : function (data) {
				for(var portal in data.books){
					$bundles.tpls.publication_portal({
						portal:portal,
						label_isbn : locale.label_isbn,
						label_title : locale.label_title,
						label_status : locale.label_status,
						label_novelty : locale.label_novelty,
						label_download : locale.label_download
					});
					for(var isbn in data.books[portal]){
						var level = data.books[portal][isbn].level*1;
						var novelty = data.books[portal][isbn].novelty;
						var cl = '';
						var selected_draft = '';

						var checked_novelty = '';

						if(level>0){
							cl = ' class="draft"';
							selected_draft = ' selected="selected"';
						}

						if(novelty){
							checked_novelty = ' checked="checked"';
						}

						$bundles.tpls.publication_item({
							title:data.books[portal][isbn].title,
							isbn:isbn,
							portal:portal,
							level:cl,
							draft:locale.draft,
							issued:locale.issued,
							selected_draft:selected_draft,
							checked_novelty:checked_novelty,
							downlod:PATH+'Admin_binary/getTEIfile/'+portal+'/'+isbn
						});
					}
				}
				[].forEach.call(document.getElementById('publication_table')
					.querySelectorAll('.pub-draft'), function (el) {
						var parent = el.parentNode.parentNode;
						switch (el.className) {
							case 'pub-draft':
								el.addEventListener('change', function(event){
									MSG.wait();
									var id = parent.getAttribute('data-id');
									var repository = parent.getAttribute('data-repository');
									$notify.pub('teiChangeLevel:'+repository,[id,repository,this.value,function(){
										parent.classList.toggle("draft");
										MSG.hide();
									}]);
								});
							break;
						}
					}
				);

				[].forEach.call(document.getElementById('publication_table')
					.querySelectorAll('input[data-type="novelty"]'), function (el) {
						var parent = el.parentNode.parentNode;
						el.addEventListener('change', function(event){
							MSG.wait();
							var id = parent.getAttribute('data-id');
							var repository = parent.getAttribute('data-repository');
							$notify.pub('teiChangeNovelty:'+repository,[id,repository,this.checked,function(){
								MSG.hide();
							}]);
						});
					}
				);
				MSG.hide();
			}
		});
	};



	var delDocs = function(books){
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'delBook',
				books : JSON.stringify(books)
			},
			success : function (data) {
				for(var portal in books){
					books[portal].forEach(function(isbn){
						var element = document.getElementById('publication_table')
							.querySelector('input[value="'+isbn+'"]').parentNode.parentNode;
						element.parentNode.removeChild(element);
					});
				}
				MSG.hide();
			}
		});
	};

	var _getSelected = function(type){
		var docs = {};
		var selected = false;
		[].forEach.call(document.getElementById('publication_table').querySelectorAll('input[data-type="'+type+'"]'), function (el) {
			if(el.checked){
				selected = true;
				var portal = el.parentNode.parentNode.getAttribute('data-repository');
				if(docs[portal]==undefined)
					docs[portal] = [];
				docs[portal].push(el.value);
			}
		});
		return {
			selected : selected,
			docs : docs
		};
	};

	var publicationView = {
		init : function () {
			locale = $definition.get('i18n!bdl/publication/locale/publication');
			getDocs();
			document.getElementById('delete_allselect').addEventListener('click', function(event){
				[].forEach.call(document.getElementById('publication_table').querySelectorAll('.pub-selector'), function (el) {
					el.checked = true;
				});
			});
			document.getElementById('delete_allunselect').addEventListener('click', function(event){
				[].forEach.call(document.getElementById('publication_table').querySelectorAll('.pub-selector'), function (el) {
					el.checked = false;
				});
			});


			document.getElementById('delete_del').addEventListener('click', function(event){
				var sel = _getSelected('del');
				if(sel.selected){
					MSG.confirm(locale.delete_title,'<p>'+locale.delete_query+'</p>',function(v){
						if(v){
							MSG.wait();
							delDocs(sel.docs);;
						}
					});
				}
			});

		}
	};
	return publicationView;
});
