/**
* __PORTALDEFAULT__ shearch
*/
(function(undefined) {

	var nowYear = null;

	var updateHistoric = function(historicEl,searchHistoric){
		[].forEach.call(historicEl.querySelectorAll('option'), function (el) {
			if(el.value!='null')
				el.parentNode.removeChild(el);
		});
		var historicSelect = [];
		searchHistoric.forEach(function(item,i){
			historicSelect.push('<option value="'+i+'">'+item.name+'</option>');
		});
		historicEl.insertAdjacentHTML('beforeend', historicSelect.join(''));
	};

	var search = function(query){
		var book = query.book;
		$dialog.wait();
		query = JSON.stringify(query);
		sessionStorage.setItem('searchLast',query);
		var d = new Date();
		var t1 = d.getTime();
		$getJSON({
			url : 'index.php',
			data : {
				module : 'Search',
				action : 'index',
				query : query,
				book : book
			},
			success : function(data){
				$frieze.occurrences(data.content);
				d = new Date();
				console.log((d.getTime() - t1) / 1000);

				$dialog.hideDelay();
			}
		});
	};

	var msgTable = function(el,count){
		var bookL = LOCALES.book_p;
		if(count<2)
			bookL = LOCALES.book;
		el.innerHTML = '<span class="frame_book">'+count+bookL+'</span>';
	};

	var msgOcc = function(el,count){
		var bookL = LOCALES.book_p;
		var occL = LOCALES.occMsg_p;

		if(count.book<2)
			bookL = LOCALES.book;

		if(count.occ<2)
			occL = LOCALES.occMsg;
		el.innerHTML = '<span class="frame_occ">'+count.occ+occL+
			' </span><span class="frame_book">'+
			count.book+bookL+'</span>';
	};

	var toBookmark = function(event){
		if(event.target && (event.target.nodeName == "B" || event.target.nodeName == 'SPAN') ) {
			event.preventDefault();
			var cl = event.target.getAttribute('class');
			if(cl=="bookmark"){
				// TODO : ajouter le numéro de page du bookmark
				var parent = event.target.parentNode;
				var file = parent.getAttribute('data-file');
				var book = file.split('_');
				file = file.replace('_','/');
				var leftEl = parent.querySelector('.left');
				var leftTxt = leftEl.innerText || leftEl.textContent;
				var rightEl = parent.querySelector('.right');
				var rightTxt = rightEl.innerText || rightEl.textContent;
				$citation.insert({
					zord_type : 'search',
					book : book[0],
					zord_url : PATH+file,
					zord_citation : leftTxt+' '+rightTxt
				});
			} else {
				var parent = event.target.parentNode;
				if(event.target.nodeName=='B')
					parent = parent.parentNode;

				var file = parent.getAttribute('data-file');
				var mark = parent.getAttribute('data-mark');
				var match = parent.getAttribute('data-match');
				if(file && mark && match){
					sessionStorage.setItem("getSearch", 'true');
					sessionStorage.setItem("mark", mark);
					sessionStorage.setItem("match", match);
					sessionStorage.setItem("mutli", false);

					var searchLast = sessionStorage.getItem('searchLast');
					if(searchLast)
						searchLast = JSON.parse(searchLast);

					if(searchLast && /"/.test(searchLast.name) && / /.test(match))
						sessionStorage.setItem("mutli", true);

					file = file.replace('_','/');
					window.location.href = PATH+file;
				}
			}
		}
	};

	document.addEventListener("DOMContentLoaded", function(event) {
		// elements
		var formSearchEl = document.getElementById('form_search');
		var queryEl = document.getElementById('query');
		var categoryEl = document.getElementById('search_category');
		var searchStartEl = document.getElementById('searchStart');
		var searchEndEl = document.getElementById('searchEnd');
		var searchInIndexEl = document.getElementById('searchInIndex');
		var historicEl = document.getElementById('searchHistoricSelect');
		var tableSourceEl = document.getElementById('publications_source');
		var tableNosourceEl = document.getElementById('publications_nosource');
		var tableBiblioEl = document.getElementById('publications_biblio');

		var frameSourceEl = document.getElementById('search_frame_source');
		var frameNosourceEl = document.getElementById('search_frame_nosource');
		var frameBiblioEl = document.getElementById('search_frame_biblio');

		var occSourceEl = document.getElementById('occurrences_source');
		var occNosourceEl = document.getElementById('occurrences_nosource');
		var occBiblioEl = document.getElementById('occurrences_biblio');

		var nosourceMsgEl = document.getElementById('search_nosourceMsg');
		var sourceMsgEl = document.getElementById('search_sourceMsg');
		var biblioMsgEl = document.getElementById('search_biblioMsg');

		var searchFilterBlockEl = document.getElementById('search_filter_block');

		var headOccSourceEl = document.getElementById('occurrences_header_source');
		var headOccNoSourceEl = document.getElementById('occurrences_header_nosource');
		var headOccBiblioEl = document.getElementById('occurrences_header_biblio');

		// -------------------------------------------------------------------
		// VARS & DISPPLAY

		// historic
		var searchHistoric = sessionStorage.getItem('searchHistoric');
		if(searchHistoric==null)
			searchHistoric = [];
		else
			searchHistoric = JSON.parse(searchHistoric);

		// search in book & last
		var searchInBook = sessionStorage.getItem('searchInBook');
		var searchLast = sessionStorage.getItem('searchLast');

		// current year
		nowYear = new Date().getFullYear();
		searchStartEl.setAttribute('max',nowYear);
		searchEndEl.setAttribute('max',nowYear);

		var occTPL = {
			lang : {
				before : LOCALES.occBefore,
				after : LOCALES.occAfter,
				before_p : LOCALES.occBefore_p,
				after_p : LOCALES.occAfter_p
			},
			tpl : [
				'creation_date_i',
				'creation_date_after_i',
				'creator_ss',
				'title',
				'editor_ss',
				'date_i'
			]
		};

		// -------------------------------------------------------------------
		// EVENTS

		// filters
		document.getElementById('search_filter').addEventListener("click", function(event) {
			var cl = this.getAttribute('class');
			if(cl=='close'){
				searchFilterBlockEl.style.display = "block";
				this.setAttribute('class','open');
			} else {
				searchFilterBlockEl.style.display = "none";
				this.setAttribute('class','close');
			}
		});

		// select historic
		historicEl.addEventListener("change", function(event) {
			if(this.value!='null'){
				generateSearch(searchHistoric[this.value]);
				search(searchHistoric[this.value]);
			}
		});

		// event search
		var _eventSearch = function(event){
			event.preventDefault();
			event.stopPropagation();
			toSearch(true);
		};

		// detect constraint validation API
		if($html5.constraintValidation(formSearchEl)){
			// search click
			document.getElementById('searchButton').addEventListener("click", function(event) {
				if(formSearchEl.checkValidity())
					_eventSearch(event);
			});
		} else {
			// search click
			document.getElementById('searchButton').addEventListener("click", function(event) {
				if(queryEl.value!='')
					_eventSearch(event);
			});
			// search enter keyboard
			queryEl.addEventListener("keyup", function(event) {
				if(event.keyCode==13){
					if(queryEl.value!='')
						_eventSearch(event);
				}
			});
		}

		// clear
		document.getElementById('clear').addEventListener("click", function(event) {
			queryEl.value = '';
			searchEndEl.value = '';
			searchStartEl.value = '';
			$frieze.refrech();
		});

		// frames
		var tables = ['source','nosource','biblio'];
		var show = function(name){
			tables.forEach(function(n){
				var elT = document.getElementById('search_frame_'+n);
				var elF = document.getElementById('search_frame_select_'+n);
				if(name==n){
					elT.style.display = 'block';
					if(!elF.classList.contains('paneselect'))
						elF.classList.add('paneselect');
				} else {
					elT.style.display = 'none';
					if(elF.classList.contains('paneselect'))
						elF.classList.remove('paneselect');
				}
			});
		};
		tables.forEach(function(name){
			document.getElementById('search_frame_select_'+name).addEventListener("click", function(event) {
				show(name);
			});
		});

		// bookmark
		occSourceEl.addEventListener("click", function(event) {
			toBookmark(event);
		});
		occNosourceEl.addEventListener("click", function(event) {
			toBookmark(event);
		});

		// -------------------------------------------------------------------
		// FUNCTIONS

		var generateSearch = function(queryParams){
			[].forEach.call(categoryEl.querySelectorAll('input'), function (el) {
				el.checked = false;
			});
			queryParams.book = null;
			queryParams.query.forEach(function(item){
				var key = Object.keys(item)[0];
				if(key=='category_ss')
					categoryEl.querySelector('input[value="'+item[key]+'"]').checked = true;
				else if(key=='text')
					queryEl.value = item[key];
				else if(key=='book_s')
					queryParams.book = item[key];
			});
			queryParams.range.forEach(function(item){
				var key = Object.keys(item)[0];
				if(key=='date_i'){
					searchStartEl.value = item[key].min;
					searchEndEl.value = item[key].max;
				} else if(key='contentType_i'){
					searchInIndexEl.checked = true;
				}
			});
		};

		var toSearch = function(historic){
			var _q = {
				name : queryEl.value,
				query : [{'text' : queryEl.value}]
			};

			[].forEach.call(categoryEl.querySelectorAll('input'), function (el) {
				if(el.checked)
					_q.query.push({'category_ss' : el.value});
			});

			var startV = searchStartEl.value;
			var endV = searchEndEl.value;

			if(startV!='' && endV=='')
				endV = nowYear;

			if(startV=='' && endV!='')
				startV = 0;

			if(startV!='' || endV!=''){
				startV = startV*1;
				endV = endV*1;
				if(startV>endV){
					startV = 0;
					endV = nowYear;
				}
			}

			searchStartEl.value = startV;
			searchEndEl.value = endV;

			_q.range = [{'date_i':{'min':startV,'max':endV}}];

			// index/biblio...
			if(searchInIndexEl.checked){
				_q.range.push({'contentType_i':{'min':0,'max':1}});
			} else {
				_q.query.push({'contentType_i' : 0});
			}

			if(historic){
				if(searchHistoric.length>20)
					searchHistoric.shift();

				searchHistoric.push(_q);
				sessionStorage.setItem('searchHistoric',JSON.stringify(searchHistoric));
				updateHistoric(historicEl,searchHistoric);
			}
			search(_q);
		};

		// -------------------------------------------------------------------
		// INIT

		// frieze
		$frieze.init(
			{
				documents : DOCS,
				start : 1510,
				end : 1620,
				steps : [{bars:11,increment:10},{bars:10,increment:1}],
				//steps : [{bars:4,increment:10},{bars:5,increment:2},{bars:2,increment:1}],,
				bars : {
					element:document.getElementById('frieze'),
					height:120,
					width:750
				},
				lang : {
					noresult : LOCALES.noresult
				},
				tableEl : tableSourceEl,
				tableCategEl : tableBiblioEl,
				tableBodyEl : document.getElementById('publicationsBody_source'),
				tableBodyCategEl : document.getElementById('publicationsBody_biblio'),
				occurrencesEl : occSourceEl,
				occurrencesCategEl : occBiblioEl,
				tableTPL : {
					table : false,
					tpl : [
						'creation_date_i',
						'creation_date_after_i',
						'creator_ss',
						'title',
						'editor_ss',
						'date_i'
					]
				},
				occTPL : occTPL,
				tableMsg : function(count){
					headOccSourceEl.style.display = 'none';
					msgTable(sourceMsgEl,count);
				},
				tableCategMsg : function(count){
					headOccBiblioEl.style.display = 'none';
					msgTable(biblioMsgEl,count);
				},
				tableNosource : function(docs){
					var table = new window.TableHTML({
						table : false,
						tpl : [
							'creator_ss',
							'title',
							'editor_ss',
							'date_i'
						]
					});
					occNosourceEl.style.display = 'none';
					tableNosourceEl.style.display = 'table';
					docs.forEach(function(data){
						table.set(data);
					});
					document.getElementById('publicationsBody_nosource').innerHTML = table.get();
					msgTable(nosourceMsgEl,table.getCounter());
				},
				occMsg : function(count){
					if(count.occ!=0)
						headOccSourceEl.style.display = 'block';
					else
						headOccSourceEl.style.display = 'none';

					msgOcc(sourceMsgEl,count);
				},
				occCategMsg : function(count){
					if(count.occ!=0)
						headOccBiblioEl.style.display = 'block';
					else
						headOccBiblioEl.style.display = 'none';

					msgOcc(biblioMsgEl,count);
				},
				occNosource : function(docs,search){
					// show occurences nosource
					tableNosourceEl.style.display = 'none';
					occNosourceEl.style.display = 'block';

					var OccHTML = new window.OccHTML(occTPL);
					var result = '<p class="no-result">'+LOCALES.noresult+'</p>';
					if(search.response.numFound>0 && docs.length>0){
						docs.forEach(function(doc){
							OccHTML.createSection(doc);
							OccHTML.setTitle(doc);
							search.response.docs.forEach(function(d,i){
								if(doc.book_s == d.book_s+'')
									OccHTML.setOcc(d,search.highlighting);
							});
							OccHTML.closeSection();
						});
						result = OccHTML.get();

						headOccNoSourceEl.style.display = 'block';
					} else {
						headOccNoSourceEl.style.display = 'none';
					}
					occNosourceEl.innerHTML = result;
					msgOcc(nosourceMsgEl,OccHTML.getCounters());
				},
				category : 'bib',

			}
		);

		// sort tables elements
		new Tablesort(tableSourceEl);
		new Tablesort(tableNosourceEl);
		new Tablesort(tableBiblioEl);

		// historic
		updateHistoric(historicEl,searchHistoric);

		// context
		if(searchInBook!=null){
			searchInBook = JSON.parse(searchInBook);
			sessionStorage.removeItem('searchInBook');
			var searchInBookQuery = {
				name : searchInBook.value,
				query : [{'text' : searchInBook.value},{'book_s': searchInBook.book}],
				range : [{'date_i':{'min':'','max':''}}]
			};
			generateSearch(searchInBookQuery);
			search(searchInBookQuery);
		} else if(searchLast!=null){
			searchLast = JSON.parse(searchLast);
			generateSearch(searchLast);
			search(searchLast);
		} else {
			$frieze.start();
		}
	});
})();
