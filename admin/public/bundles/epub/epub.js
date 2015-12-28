/**
 * Bundle epub
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
define('bdl/epub/epub',function() {
	var getEpubs = function(){
		MSG.wait();
		document.getElementById('epubs_table').insertComponent('replace','<div class="tabs" id="epubs_tabs"></div><div class="panels" id="epubs_panels"></div>');
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'getEpubs'
			},
			success : function (data) {
				var tplPanelContent = $bundles.tpls.epubs_item;
				for(var portal in data.books){
					$bundles.tpls.epubs_tab({
						portal:portal
					});
					var content = '';
					for(var isbn in data.books[portal]){
						var isEpub = false;

						if(data.epubs[portal]!=undefined && data.epubs[portal].indexOf(isbn)>-1)
							isEpub = true;

						var level = data.books[portal][isbn].level*1;
						var cl = '';
						if(level>0)
							cl = ' class="draft"';
						var title = data.books[portal][isbn].title.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
							 return '&#'+i.charCodeAt(0)+';';
						});
						var check = '&#160;&#160;';
						var download = '&#160;&#160;';
						if(isEpub){
							title = '<a href="'+PATH+'%3Fmodule%3DAdminconnect%26action%3Dliseuse%26epub%3D'+isbn+'%26portal%3D'+portal+'" target="_blank">'+title+'</a>';
							check = '<span data-isbn="'+isbn+'" data-portal="'+portal+'">✔</span>';
							download = '<a href="'+PATH+'Admin_binary/getEPUBfile/'+portal+'/'+isbn+'">↥</a>';
						}
						var _data = {
							title:title,
							isbn:isbn,
							portal:portal,
							level:cl,
							check:check,
							download:download
						}
						content += $tpl.render(tplPanelContent, _data);;
					}
					$bundles.tpls.epubs_panels({
						portal:portal,
						content : content
					});
				}
				$panelsTabs(document.getElementById('epubs_table'));
				[].forEach.call(document.getElementById('epubs_table').querySelectorAll('span[data-isbn]'), function (el) {
					el.addEventListener('click', function(event){
						MSG.wait();
						var isbn = this.getAttribute('data-isbn');
						var portal = this.getAttribute('data-portal');
						$ajax.getJSON({
							url : 'index.php',
							data : {
								module : 'Admin_action',
								action : 'getEpubCheck',
								book : isbn,
								portal : portal
							},
							success : function (data) {

								MSG.box('<textarea id="_epub_check">'+data.content+'</textarea><div><button id="_epub_checkButton">Close</button></div>',true,function(){
									document.getElementById('_epub_checkButton').addEventListener('click', function(event){
										MSG.hideModal();
										MSG.hide();
									});
								});
							}
						});
					});
				});
				MSG.hide();
			}
		});
	};
	var createEpubs = function(books){
		MSG.waitM('<div id="_epub_counter"></div><div id="_epub_book"></div><div id="_epub_parse"><div id="_epub_bar"></div></div>',true);
		var pos = 0;
		var _cr = function(val){
			var barEL = document.getElementById('_epub_bar');

			if(books.length-1>=val){
				document.getElementById('_epub_counter').innerHTML = (val+1)+' / '+books.length;
				var book = books[val];
				document.getElementById('_epub_book').innerHTML = book.id;
				pos++;
				$ajax.getJSON({
					url : 'index.php',
					data : {
						module : 'Admin_action',
						action : 'createEpubs',
						book : JSON.stringify(book)
					},
					success : function (data) {
						barEL.innerHTML = '';
						barEL.style.width = '0px';
						width = 200/data.content.length;
						$req('library/TEITOHTML_'+book.portal,function(transform){
							transform.initBook();
							var filesPos = 0;
							var teiToHtml = function(_val){
								if(data.content.length-1>=_val){
									var fileTei = data.content[_val];
									filesPos++;
									data.content[_val].tei = transform.getTei(data.content[_val].tei,data.content[_val].title);
									setTimeout(function(){
										barEL.style.width = ((_val+1)*width)+'px';
										teiToHtml(filesPos);
									},50);
								} else {
									barEL.innerHTML = 'SAVE + CHECK';
									data.medias = transform.getMedias();
									data.navMap = transform.getNavMap(data.navMap);
									data.portal = book.portal;
									data.id = book.id;
									$ajax.getJSON({
										url : 'index.php',
										data : {
											module : 'Admin_action',
											action : 'saveEpubs',
											data : JSON.stringify(data)
										},
										success : function () {
											_cr(pos);
										}
									});
								}
							};
							teiToHtml(0);
						});
					}
				});
			} else {
				MSG.hide();
				MSG.hideModal();
				getEpubs();
			}
		};

		_cr(pos);
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
				var epubs = [];
				var create = false;
				[].forEach.call(document.getElementById('epubs_table').querySelectorAll('input'), function (el) {
					if(el.checked){
						create = true;
						var portal = el.getAttribute('data-portal');
						epubs.push({portal:portal,id:el.value});
					}
				});
				if(create)
					createEpubs(epubs);
			});
		}
	};
	return epubView;
});
