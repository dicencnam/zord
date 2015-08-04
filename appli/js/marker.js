
document.addEventListener("DOMContentLoaded", function(event) {
	var er_key = /\{\{\$([\w\.]*)\}\}/g;
	var render = function (template, data) {
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
	};
	var citations = sessionStorage.getItem('citations');

	if(!citations)
		citations = {};
	else
		citations = JSON.parse(citations);



	// Initialize a system object, which contains two methods needed by the
	// engine.
	var citeprocSys = {
		// Given a language tag in RFC-4646 form, this method retrieves the
		// locale definition file.  This method must return a valid *serialized*
		// CSL locale. (In other words, an blob of XML as an unparsed string.  The
		// processor will fail on a native XML object or buffer).
		retrieveLocale: function (lang){
				var xhr = new XMLHttpRequest();
				xhr.open('GET', PATH+'csl/locales/locales-' + document.documentElement.lang + '.xml', false);
				xhr.send(null);
				return xhr.responseText;
		},

		// Given an identifier, this retrieves one citation item.  This method
		// must return a valid CSL-JSON object.
		retrieveItem: function(id){
				return citations[id];
		}
	};
	// Given the identifier of a CSL style, this function instantiates a CSL.Engine
	// object that can render citations in that style.
	var getProcessor = function (styleID) {
		// Get the CSL style as a serialized string of XML
		var xhr = new XMLHttpRequest();
		xhr.open('GET', PATH+'csl/styles/' + styleID + '.csl', false);
		xhr.send(null);
		var styleAsText = xhr.responseText;

		// Instantiate and return the engine
		var citeproc = new CSL.Engine(citeprocSys, styleAsText);
		return citeproc;
	};

	var saveCitations = function(){
		sessionStorage.setItem('citations',JSON.stringify(citations));
	};

	var saveChangeNote = function(el){
		el.addEventListener("keyup", function(event) {
			var parent = el.parentNode;
			var id = parent.getAttribute('data-id');
			citations[id].zord_note = this.value;
			saveCitations();
		});
	};

	// This runs at document ready, and renders the bibliography
	var renderBib = function (){
		var citeproc = getProcessor(document.getElementById('marker_styles_select').value);
		var html = [];
		for (var key in citations) {
				var itemIDs = [];
				itemIDs.push(key);
				citeproc.updateItems(itemIDs);

				var bibResult = citeproc.makeBibliography(key);
				html.push('<div class="marker" data-id="'+key+'">');
				html.push('<span class="marker-del" data-tooltip="'+LABEL_DELCITATION+'">-</span>');
				if(citations[key].zord_note == undefined || citations[key].zord_note == '')
					html.push('<span class="marker-addnote" data-tooltip="'+LABEL_ADDNOTE+'">≡</span>');

				html.push('<div class="marker-bib">'+bibResult[1].join('')+'</div>');

				if(citations[key].zord_URL != undefined)
					html.push('<div class="marker-url"><a href="'+citations[key].zord_URL+'"  target="_blank">'+citations[key].zord_URL+'</a></div>');

				if(citations[key].zord_citation != undefined)
					html.push('<div class="marker-citation">'+citations[key].zord_citation+'</div>');

				if(citations[key].zord_note != undefined && citations[key].zord_note != '')
					html.push('<textarea class="marker-note">'+citations[key].zord_note+'</textarea>');

				html.push('<hr/></div>');

		}
		document.getElementById('markers').innerHTML = html.join('');
		[].forEach.call(document.querySelectorAll('.marker-del'), function (el) {
			el.addEventListener("click", function(event) {
				var parent = el.parentNode;
				var id = parent.getAttribute('data-id');
				delete citations[id];
				saveCitations();
				renderBib();
			});
		});

		[].forEach.call(document.querySelectorAll('.marker-addnote'), function (el) {
			el.addEventListener("click", function(event) {
				var parent = el.parentNode;
				parent.insertAdjacentHTML('beforeend', '<textarea class="marker-note"></textarea>');
				parent.removeChild(this);
				saveChangeNote(parent.querySelector('.marker-note'));
			});
		});

		[].forEach.call(document.querySelectorAll('.marker-note'), function (el) {
			saveChangeNote(el);
		});

	};

	document.getElementById('marker_styles_select').addEventListener("change", function(event) {
		renderBib();
	});

	document.getElementById('markers_export').addEventListener("click", function(event) {
		$getJSON({
			url : 'index.php',
			data : {
				module : 'Search',
				action : 'createCitations',
				content : document.getElementById('markers').innerHTML
			},
			success : function(data){
				document.body.insertAdjacentHTML('beforeend', "<iframe src='"+PATH+'Motor/getCitationsFile'+
					"' style='display: none;' ></iframe>");
			}
		});
	});

	document.getElementById('markers_clear').addEventListener("click", function(event) {
		sessionStorage.removeItem('citations');
		citations = {};
		renderBib();
	});

	renderBib();
});
