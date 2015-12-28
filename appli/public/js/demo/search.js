/**
* demo search
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
			'<br/></span><span class="frame_book">'+
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
		var searchBookEl = document.getElementById('searchBook');
		var categoryEl = document.getElementById('search_category');
		var searchStartEl = document.getElementById('searchStart');
		var searchEndEl = document.getElementById('searchEnd');
		var searchInIndexEl = document.getElementById('searchInIndex');
		var historicEl = document.getElementById('searchHistoricSelect');
		var searchFilterBlockEl = document.getElementById('search_filter_block');

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

		var occSourceTPL = {
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

		var occNoSourceTPL = {
			lang : {
				before : LOCALES.occBefore,
				after : LOCALES.occAfter,
				before_p : LOCALES.occBefore_p,
				after_p : LOCALES.occAfter_p
			},
			tpl : [
				'creator_ss',
				'title',
				'editor_ss',
				'date_i'
			]
		};

		var tableSourceTPL = {
			table : false,
			tpl : [
				'creation_date_i',
				'creation_date_after_i',
				'creator_ss',
				'title',
				'editor_ss',
				'date_i'
			]
		};

		var tableNoSourceTPL = {
			table : false,
			tpl : [
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
		$panelsTabs(document.getElementById('search_frames'));

		// bookmark
		var dzOCC = ['dzOcc_source','dzOcc_nosource'];
		dzOCC.forEach(function(id){
			document.getElementById(id).addEventListener("click", function(event) {
				toBookmark(event);
			});
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
				else if(key=='book_s'){
					queryParams.book = item[key];
				}
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

			if(searchBookEl.style.display == 'block'){
				_q.query.push({'book_s' : searchBookEl.getAttribute('data-book')});
			}

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
					height:100,
					width:740
				},
				lang : {
					noresult : LOCALES.noresult
				},
				prefix : 'dz',
				msgTable : msgTable,
				msgOcc : msgOcc,
				defineFrame : [
					{
						name : 'source',
						type : 'source',
						tableTPL : tableSourceTPL,
						occTPL : occSourceTPL,
						categories : ['source']
					},
					{
						name : 'nosource',
						type : 'nosource',
						tableTPL : tableNoSourceTPL,
						occTPL : occNoSourceTPL,
						categories : ['nosource']
					}
				]
			}
		);

		// historic
		updateHistoric(historicEl,searchHistoric);

		// context
		if(searchInBook!=null){
			searchInBook = JSON.parse(searchInBook);
			sessionStorage.removeItem('searchInBook');
			var category_ss = [];
			[].forEach.call(categoryEl.querySelectorAll('input'), function (el) {
				category_ss.push({'category_ss' :el.value});
			});
			var searchInBookQuery = {
				name : searchInBook.value,
				query : [{'text' : searchInBook.value},{'book_s': searchInBook.book}],
				range : [{'date_i':{'min':'','max':''}}]
			};
			searchBookEl.style.display = 'block';
			searchBookEl.setAttribute('data-book',searchInBook.book);
			document.getElementById('searchBookTxt').innerHTML = '<a href="'+PATH+searchInBook.book+'">'+searchInBook.title+'</a>';
			searchInBookQuery.query = searchInBookQuery.query.concat(category_ss);
			generateSearch(searchInBookQuery);
			search(searchInBookQuery);
		} else if(searchLast!=null && !SEARCHLAST){
			searchLast = JSON.parse(searchLast);
			generateSearch(searchLast);
			search(searchLast);
		} else {
			$frieze.start();
			// sort tables elements
			[].forEach.call(document.getElementById('search_frames').querySelectorAll('table'), function (el) {
				new Tablesort(el);
			});
		}
	});
})();
