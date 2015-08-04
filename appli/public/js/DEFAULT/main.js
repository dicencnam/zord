/**
* __PORTALDEFAULT__ main
*/
var PADMAX = 71;
window.$getJSON = function(properties){
	var queryString = function ( data ) {
		var result = [];
		for (var key in data)
			result.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
		return result.join( "&" ).replace( /%20/g, "+" );
	};
	var stateChange = function () {
		if (this.readyState===4) {
			if(this.status===200) {
				if(properties.success!==undefined)
					properties.success(JSON.parse(this.responseText));
			} else {
				if(properties.error!==undefined)
					properties.error(this.status,JSON.parse(this.responseText));
			}
		}
	};
	var request = new XMLHttpRequest();
	request.onreadystatechange = stateChange;
	request.open("POST", properties.url , true);
	request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	request.send(queryString(properties.data));
};

window.$getJS = function(file,callback){
	var sc = document.createElement("script");
	sc.setAttribute("type", "text/javascript");
	sc.setAttribute("src", PATH+file+'.js');
	sc.addEventListener("load",function () {
		callback();
	}, false);
	document.getElementsByTagName('head')[0].appendChild(sc);
};

window.$scrollTop = {
	set : function(value){
		window.scrollTo(0, value);
	},
	get : function(){
		return document.body.scrollTop || document.documentElement.scrollTop;
	}
};

window.$html5 = {
	isHostMethod : function(o, m) {
			var t = typeof o[m], reFeaturedMethod = new RegExp('^function|object$', 'i');
			return !!((reFeaturedMethod.test(t) && o[m]) || t === 'unknown');
	},
	constraintValidation : function(field) {
			return this.isHostMethod(field,"checkValidity");
	}
};

// signets/citations
window.$citation = {
	push : function(cite){
		$getJSON({
			url : 'index.php',
			data : {
				module : 'Search',
				action : 'csl',
				book : cite.book
			},
			success : function(data){
				var cslCite = data.content;
				if(cite.zord_note!='')
					cslCite.zord_note = cite.zord_note;

				if(cite.zord_citation!=undefined && cite.zord_citation!='')
					cslCite.zord_citation = $citation._clearCitation(cite.zord_citation);

				if(cite.page!=undefined && cite.page!='')
					cslCite.page = cite.page;

				if(cite.zord_url!=undefined && cite.zord_url!='')
					cslCite.zord_URL = cite.zord_url;

				var citations = sessionStorage.getItem('citations');

				if(!citations)
					citations = {};
				else
					citations = JSON.parse(citations);

				citations[cslCite.id] = cslCite;

				sessionStorage.setItem('citations',JSON.stringify(citations));
			}
		});
	},
	pushBug : function(cite){
		$getJSON({
			url : 'index.php',
			data : {
				module : 'Search',
				action : 'bugSave',
				bug : JSON.stringify(cite)

			},
			success : function(data){
				console.log(data);
			}
		});
	},
	noSelect : function(type){
		if(type=='bug'){
			$dialog.box(document.getElementById('template_dialog_help').innerHTML, function(dialogEl){
				dialogEl.querySelector('div[data-id="content"]').innerHTML = document.getElementById('template_dialog_bug_help').innerHTML;
				dialogEl.querySelector('button[data-id="dialog_help_close"]')
					.addEventListener("click", function(event) {
						$dialog.hide();
					}
				);
			});
		}
	},

	insert : function(data){
		// dialog box
		if(data.zord_type=='citation'){
			$dialog.box(document.getElementById('template_dialog_citation').innerHTML, function(dialogEl){
				var commentEl = dialogEl.querySelector('textarea[data-id="dialog_citation_comment"]');
				dialogEl.querySelector('button[data-id="dialog_citation_ok"]')
					.addEventListener("click", function(event) {
						data.zord_note = commentEl.value;
						$citation.push(data);
						$dialog.waitMsg(document.getElementById('template_dialog_citation_valid').innerHTML);
					}
				);
				dialogEl.querySelector('button[data-id="dialog_citation_cancel"]')
					.addEventListener("click", function(event) {
					$dialog.hide();
				});
			});
		} else if(data.zord_type=='bug'){
			$dialog.box(document.getElementById('template_dialog_bug').innerHTML, function(dialogEl){
				var commentEl = dialogEl.querySelector('textarea[data-id="dialog_bug_comment"]');
				dialogEl.querySelector('button[data-id="dialog_bug_ok"]')
					.addEventListener("click", function(event) {
						data.zord_note = commentEl.value;
						$citation.pushBug(data);
						$dialog.waitMsg(document.getElementById('template_dialog_bug_valid').innerHTML);
					}
				);
				dialogEl.querySelector('button[data-id="dialog_bug_cancel"]')
					.addEventListener("click", function(event) {
					$dialog.hide();
				});
			});
		}
	},
	init : function() {
		var t = ['{"nspace":"'];
		for (b=0; b<IDS.length; b=b+2)
			t.push(String.fromCharCode(parseInt(IDS.substr(b, 2), 16)));
		window.ELS = JSON.parse(t.join('')+'"}}');
	},
	_clearCitation : function(citation) {
		var div = document.createElement('div');
		div.innerHTML = citation;
		var _insert = function(el,s,e){
			el.insertAdjacentHTML('afterbegin', '¡§¡'+s+'¡¿¡');
			el.insertAdjacentHTML('beforeend', '¡§¡/'+e+'¡¿¡');
		};
		var hi = {
			sup :['sub','sub'],
			b :['b','b'],
			sc :['span style="font-variant:small-caps;"','span'],
			n :['span','span'],
			small :['span style="font-size:0.8em;"','span'],
			i :['i','i'],
			underline :['span style="text-decoration:underline;"','span'],
			big :['span style="font-size:1.2em;"','span']
		};

		[].forEach.call(div.querySelectorAll('*'), function (el) {
			var nodn = el.nodeName.toLowerCase();
			switch(nodn){
				case ELS.nspace+':'+ELS.note.elm:
					var n = el.getAttribute(ELS.note.n);
					if(n!=undefined)
						el.insertAdjacentHTML('beforebegin', '¡§¡sup¡¿¡'+n+'¡§¡/sup¡¿¡');
				break;
				case ELS.nspace+':'+ELS.ref.elm:
					_insert(el,'sup','sup');
				break;
				case ELS.nspace+':'+ELS.emph.elm:
					_insert(el,'em','em');
				break;
				case ELS.nspace+':'+ELS.p.elm:
					el.insertAdjacentHTML('afterend', '¡§¡br/¡¿¡');
				break;
				case ELS.nspace+':'+ELS.head.elm:
					_insert(el,'p style="font-size:1.3em;"','p');
				break;
				case ELS.nspace+':'+ELS.hi.elm:
					var rend = el.getAttribute(ELS.hi.rend);
					if(n!=undefined)
						_insert(el,hi[rend][0],hi[rend][1]);
				break;
			}
		});
		return div.innerHTML.replace(/<\/?[^>]+>/g, '').replace(/¡§¡/g, '<').replace(/¡¿¡/g, '>');
	}
};

window.$highlight = (function(undefined) {
	var count = 0;
	var toMove = function(node, pat){
		var dataPat = node.querySelectorAll('span[data-pat="'+pat+'"]');
		var lengthPat = dataPat.length;
		[].forEach.call(dataPat, function (el,i) {
			var pos = i+1;
			var posL = pos - 1;
			var posR = pos + 1;
			if(lengthPat>1){
				if(posL==0)
					posL = lengthPat;
				if(posR>lengthPat)
					posR = 1;
				el.setAttribute('data-mark',pos);
				el.removeAttribute('data-pat');
				el.insertAdjacentHTML('afterbegin','<span data-pos="'+posL+'"></span>');
				el.insertAdjacentHTML('beforeend','<span data-pos="'+posR+'"></span>');
				[].forEach.call(el.querySelectorAll('span[data-pos]'), function (span) {
					span.addEventListener("click", function(event) {
						var dataPos = this.getAttribute('data-pos');
						var el = document.querySelector('span[data-mark="'+dataPos+'"]');
						if(el)
							window.scrollTo(el.offsetLeft,el.offsetTop-75);
					});
				});

			} else {
				el.setAttribute('data-mark',1);
				el.removeAttribute('data-pat');
			}
		});
	};
	var highlight = function (node, pat, regex) {
		var skip = 0;
		if (node.nodeType == 3) {
			var pos = node.data.search(regex);
			if (pos >= 0 && node.data.length > 0) {
				var match = node.data.match(regex);
				var spannode = document.createElement('span');
				spannode.className = 'searchSelect';
				spannode.setAttribute('data-pat',pat);
				var middlebit = node.splitText(pos);
				var endBit = middlebit.splitText(match[0].length);
				var middleclone = middlebit.cloneNode(true);
				spannode.appendChild(middleclone);
				middlebit.parentNode.replaceChild(spannode, middlebit);
				skip = 1;
			}
		} else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
			for (var i = 0; i < node.childNodes.length; ++i)
				i += highlight(node.childNodes[i], pat, regex);
		}
		return skip;
	};

	var toHighlight = function(node, match, mark, multi){
		var _pat = [];
		if(multi)
			_pat.push(match);
		else
			_pat = match.split(' ');
		_pat.forEach(function(p){
			pu = p.toUpperCase();
			var reg = new RegExp('\\b(?:'+p+')\\b', 'i');
			highlight(node,pu,reg);
			toMove(node,pu);
		});
		setTimeout(function(){
			var el = document.querySelector('span[data-mark="'+mark+'"]');
			window.scrollTo(el.offsetLeft,el.offsetTop-60);
		},1000);
	}
	return toHighlight;
})();

document.addEventListener("DOMContentLoaded", function(event) {

	if(document.getElementById('navbar')){
		var padMin = 0;
		var heightWindow = 0;
		var setWindowHeight = function(){
			heightWindow = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
		};

		var toScroll = function(){
			var marg = PADMAX-$scrollTop.get();
			marg = (marg < padMin) ? padMin : (marg > PADMAX) ? PADMAX : marg;
			document.getElementById('navbar').style.marginTop = marg + 'px';
			if(document.getElementById('toc')!=undefined){
				document.getElementById('toc').style.marginTop = (marg+55) + 'px';
				document.getElementById('tocButton').style.height = (heightWindow-(marg+55)) + 'px';
				document.getElementById('tocContent').style.height =(heightWindow-(marg+100)) + 'px';
			}
			if(document.getElementById('tools')!=undefined){
				document.getElementById('tools').style.marginTop = (marg+55) + 'px';
			}
		};

		window.addEventListener('resize', function(event){
			setWindowHeight();
			toScroll();
		});

		document.addEventListener('scroll', function(){
			toScroll();
		});

		setWindowHeight();
		toScroll();
}

	// change language
	[].forEach.call(document.querySelectorAll('.lang-noselect'), function (el) {
		el.addEventListener("click", function(event) {
			var lang = this.getAttribute('data-lang');
			$getJSON({
				url : 'index.php',
				data : {
					module : 'Search',
					action : 'lang',
					lang : lang
				},
				success : function(){
					location.reload();
				}
			});
		});
	});

	// help dialog
	[].forEach.call(document.querySelectorAll('.help_dialog'), function (el) {
		el.addEventListener("click", function(event) {
			$dialog.box(document.getElementById('template_dialog_help').innerHTML, function(dialogEl){
				dialogEl.querySelector('div[data-id="content"]').innerHTML = el.firstChild.innerHTML;
				dialogEl.querySelector('button[data-id="dialog_help_close"]')
					.addEventListener("click", function(event) {
						$dialog.hide();
					}
				);
			});
		});
	});

	// go to page search - remove last search
	if(document.getElementById('to_disconnect')){
		document.getElementById('link_search').addEventListener("click", function(event) {
			sessionStorage.removeItem('searchLast');
		});
		document.getElementById('to_disconnect').addEventListener("click", function(event) {
			sessionStorage.clear();
		});
	}
	if(document.getElementById('to_connexion')){
		document.getElementById('to_connexion').addEventListener("click", function(event) {
			this.insertAdjacentHTML('beforebegin','<input type="hidden" value="'+location.href+'" name="lasthref"/>' );
		});
	}
});
