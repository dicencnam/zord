/**
* demo book
*/
(function(undefined) {

	var TOC = [];

	var nextLabel = '';
	var beforeLabel = '';
	var tocContentEl = null;

	var ariadneMake = function(){
		var mvPart = '';
		var mvBefore = false;
		var ariadneHtml = '';

		[].forEach.call(tocContentEl.querySelectorAll('span'), function (el) {
			el.classList.remove('part-select');
		});

		if(PART == '' || PART == 'home'){// frontispiece
			mvPart = BOOK;
			ariadneHtml = '<span data-part="'+BOOK+'">'+TITLE+'</span>';
		} else {
			var _partToc = [];
			var _partHref = BOOK+'/'+PART;
			mvPart = _partHref;
			var _partEl = tocContentEl.querySelector('li[data-part="'+_partHref+'"]');
			var __partLabel = _partEl.firstChild.textContent;
			_partToc.push({
				link : _partHref,
				label : __partLabel
			});
			var __partId = window.location.hash.substring(1);
			if(__partId){
				var __part = tocContentEl.querySelector('li[data-id="'+__partId+'"]');

				if(__part){
					__part.firstChild.classList.add("part-select");
					var __partName = __part.getAttribute('data-part');
					__partLabel = __part.firstChild.textContent;
					if(_partHref==__partName)
						_partToc = [];
					while(_partHref==__partName){
						_partToc.push({
							part :__partName,
							id : __partId,
							label : __partLabel
						});
						__part = __part.parentNode.parentNode;
						__partName = __part.getAttribute('data-part');
						__partId = __part.getAttribute('data-id');
						__partLabel = __part.firstChild.textContent;
					}
				} else {
					_partEl.firstChild.classList.add("part-select");
				}
			}

			_partToc.reverse();
			ariadneHtml = '<span class="ariadne-section" data-part="'+_partToc[0].part+'" data-id="'+_partToc[0].id+'">'+_partToc[0].label+'</span>';
			ariadneHtml = '<span data-part="'+BOOK+'">'+TITLE+'</span>'+ariadneHtml;
		}
		var before = null;
		var next = null;
		var beforeEl = '<span class="ariadne-before" style="visibility:hidden;"> </span>';
		var nextEl = '<span class="ariadne-next" style="visibility:hidden;"> </span>';
		var oldItem = null;
		var selectedItem = false;
		TOC.forEach(function(el,i){
			if(selectedItem){
				selectedItem = false;
				next = el;
			}
			if(el.part==mvPart){
				before = oldItem;
				selectedItem = true;
			}
			oldItem = el;
		});

		if(before){
			beforeEl = '<span class="ariadne-before" data-part="'+before.part+'" data-id="'+before.id+'" title="'+beforeLabel+'"> </span>';
		}
		if(next){
			nextEl = '<span class="ariadne-next" data-part="'+next.part+'" data-id="'+next.id+'" title="'+nextLabel+'"> </span>';
		}
		document.getElementById('ariadne').innerHTML = beforeEl+'<span class="ariadne-content">'+ariadneHtml+'</span>'+nextEl;
	};

	var setMarkerAnchor = function(toScroll){
		var hash = window.location.hash.substring(1);
		if(hash){
			var el = document.getElementById(hash);
			if(el){
				if(toScroll)
					$scrollTop.set(el.offsetTop - 70);
				document.getElementById('markerAnchorLeft').style.top = el.offsetTop + 'px';
				document.getElementById('markerAnchorRight').style.top = el.offsetTop + 'px';
			}
		}
	};

	window.addEventListener("hashchange", function(){
		var top = $scrollTop.get() - 70;
		if(top<PADMAX)
			top = PADMAX;
		$scrollTop.set(top);
		setMarkerAnchor(false);
		ariadneMake();
	}, false);


	document.addEventListener("DOMContentLoaded", function(event) {
		// elements
		var tocEl = document.getElementById('toc');
		tocContentEl = document.getElementById('tocContent');
		var contentTEI = document.getElementById('tei');
		var citationsEl = document.getElementById('citationsImport');
		var footnotesEl = document.getElementById('footnotes');
		nextLabel = document.getElementById('template_button_next').textContent;
		beforeLabel = document.getElementById('template_button_before').textContent;

		document.getElementById('navbar').classList.add("bookview");

		var _oldPart = null;
		[].forEach.call(tocEl.querySelectorAll('li'), function (el,i) {
			var id = el.getAttribute('data-id');
			var part = el.getAttribute('data-part');
			if(part!=undefined && _oldPart!=part){
				_oldPart = part;
				TOC.push({
					part : part,
					id : id
				});
			}
		});

		// vars
		var page = 1;
		var pageOld = 0;
		if(typeof ELS=='undefined')
			$citation.init();
		var pageSelector = ELS.nspace+'\\:'+ELS.pb.elm+'['+ELS.pb.n+']';
		var pbName = ELS.nspace+':'+ELS.pb.elm;
		var pageTEIEl = ELS.nspace+':'+ELS.pb.elm;
		var refTEIEl = ELS.nspace+':'+ELS.ref.elm;
		var refTEIElU = refTEIEl.toUpperCase();
		var citationsElWith = citationsEl.offsetWidth;

		// Find this book
		var urlBook = document.querySelector('meta[name="ref_url"]');
		if(urlBook){
			var getbookEl = document.getElementById('get_book');
			getbookEl.setAttribute('href',urlBook.getAttribute('content'));
			getbookEl.parentNode.removeAttribute('style');
		}

		// Ariadne
		ariadneMake();
		setTimeout(function(){ setMarkerAnchor(true); }, 300);


		// ------------------------------------------------------------------
		// DISPLAY ASSIGN

		citationsEl.style.display = 'none';

		// graphics ----------------------------------------------------------
		var loadGraphics = function(){
			[].forEach.call(contentTEI.querySelectorAll(ELS.nspace+'\\:'+ELS.graphic.elm), function (el,i) {
				var url = el.getAttribute(ELS.graphic.url);
				if(url!=undefined){
					url = url.replace('etc/',PATH+'medias/'+BOOK+'/');
					el.insertAdjacentHTML('afterend','<img src="'+url+'"/>');
				}
			});
		};

		var teiFiles = contentTEI.querySelectorAll(ELS.nspace+'\\:'+ELS.nspace);
		if(teiFiles.length>1){
			[].forEach.call(teiFiles, function (el,i) {
				el.classList.add("__teiFloatContent"+i);
			});

			var TeiLeft = document.querySelector('.__teiFloatContent0');
			var TeiRight = document.querySelector('.__teiFloatContent1');
			var sectionRight = TeiRight.querySelectorAll('tei\\:pb[n]:not([ed="temoin"])');
			setTimeout(function(){
				[].forEach.call(TeiLeft.querySelectorAll('tei\\:pb[n]:not([ed="temoin"])'), function (leftEl,i) {
					setTimeout(function(){
						var rightEl = sectionRight[i];
						var leftTop = leftEl.getBoundingClientRect().top;
						var rightTop = rightEl.getBoundingClientRect().top;
						if(leftTop>rightTop){
							rightEl.style.marginTop = ( (leftTop-rightTop)+20) + 'px';
						} else if(leftTop<rightTop){
							leftEl.style.marginTop = ( (rightTop-leftTop)+20) + 'px';
						}
					}, 300);
				});
			}, 500);
		}

		var facsimile = contentTEI.querySelectorAll(ELS.nspace+'\\:'+ELS.facsimile.elm);
		if(facsimile.length>0){
			$getJS('js/openseadragon.min',function(){
				[].forEach.call(facsimile, function (group,i) {
					var graphics = [];
					[].forEach.call(group.querySelectorAll(ELS.nspace+'\\:'+ELS.graphic.elm), function (el) {
						var url = el.getAttribute(ELS.graphic.url);
						if(url!=undefined){
							url = url.replace('etc/',PATH+'medias/'+BOOK+'/').replace(/\.jpg$/,'.dzi');
							graphics.push(url);
						}
					});

					[].forEach.call(group.querySelectorAll('*'), function (el) {
						el.remove();
					});
					var id = '__group'+i;
					group.setAttribute('id',id);

					var viewer = OpenSeadragon({
						id: id,
						prefixUrl: PATH+'public/img/'+WEBSITE+'/OpenSeadragon/',
						showNavigator: true,
						showRotationControl:true,
						sequenceMode: true,
						tileSources: graphics
					});
				});
				loadGraphics();
			});
		} else {
			loadGraphics();
		}

		// swicthTemoin
		var swicthTemoinEl = document.getElementById('swicthTemoin');
		var swicthTemoin = sessionStorage.getItem("swicthTemoin");
		var swicthTemoinFC = function(load){
			if(load){
				if(swicthTemoin=='true')
					swicthTemoin = 'false';
				else
					swicthTemoin = 'true';
			}
			if(swicthTemoin=='true'){
				[].forEach.call(contentTEI.querySelectorAll('tei\\:lb[ed="margin"], tei\\:pb[ed="temoin"], tei\\:pb[n]'), function (el,i) {
					el.classList.add("__swicthTemoin");
				});
				swicthTemoin = 'false';
				swicthTemoinEl.classList.add("__disabled");
			} else {
				[].forEach.call(contentTEI.querySelectorAll('tei\\:lb[ed="margin"], tei\\:pb[ed="temoin"], tei\\:pb[n]'), function (el,i) {
					el.classList.remove("__swicthTemoin");
				});
				swicthTemoin = 'true';
				swicthTemoinEl.classList.remove("__disabled");
			}
			sessionStorage.setItem("swicthTemoin", swicthTemoin);
		};
		if(swicthTemoin==null)
			swicthTemoin = 'true';
		else
			swicthTemoinFC(true);
		swicthTemoinEl.addEventListener('click', function(event){
			swicthTemoinFC(false);
		});

		// footnote ----------------------------------------------------------
		var _selectFootnote = function(elID){
			var id = 'footref_'+elID;
			document.getElementById(id).querySelector('.footnote-note').classList.add("footnote-select");
			window.location.href = '#'+id;
		};

		[].forEach.call(contentTEI.querySelectorAll(ELS.nspace+'\\:'+ELS.note.elm+', '+pageSelector), function (el,i) {
			if(el.nodeName.toLowerCase()==pbName){
				var n = el.getAttribute(ELS.pb.n);
				var ed = el.getAttribute(ELS.pb.ed);
				if(n!='undefined' && ed!='temoin')
					page = n;
			} else {
				if(pageOld!=page){
					footnotesEl.insertAdjacentHTML('beforeend','<div class="foot-page">p. '+page+'</div>');
					pageOld = page;
				}
				var place = el.getAttribute(ELS.note.place);
				var n = el.getAttribute(ELS.note.n);
				if(place!=undefined && (place=="foot" || place=="end") && n!=undefined){
					el.setAttribute('title',el.textContent);
					var content = '<div id="footref_'+el.id+
						'"><div class="footnote-counter" data-id="'+el.id+'">'+n+
						'</div><div class="footnote-note" >'+el.outerHTML+'</div></div>';

					footnotesEl.insertAdjacentHTML('beforeend', content);

					el.innerHTML = '';
					el.addEventListener('click', function(event){
						[].forEach.call(footnotesEl.querySelectorAll('.footnote-note'), function (_el,i) {
							_el.classList.remove("footnote-select");
						});
						_selectFootnote(el.id);
					});
				}
			}
		});

		// footnote counter
		[].forEach.call(contentTEI.querySelectorAll('.footnote-counter'), function (el,i) {
			el.addEventListener('click', function(event){
				window.location.href = '#'+el.getAttribute('data-id');
				[].forEach.call(footnotesEl.querySelectorAll('.footnote-note'), function (_el,i) {
					_el.classList.remove("footnote-select");
				});
			});
		});

		// highlight
		if(sessionStorage.getItem("getSearch")=='true'){
			$highlight(
				document.getElementById('tei'),
				sessionStorage.getItem('match'),
				sessionStorage.getItem('mark'),
				sessionStorage.getItem('mutli')
			);
			sessionStorage.setItem("getSearch", 'false');
		}

		// -------------------------------------------------------------------
		// EVENTS

		// toc
		tocEl.addEventListener('mouseover', function(event){
			event.preventDefault();
			event.stopPropagation();
			if(!tocEl.classList.contains("show")){
				tocEl.classList.add("show");
				var selectEl = tocEl.querySelector('.part-select');
				setTimeout(function(){
					if(selectEl)
						tocContentEl.scrollTop = selectEl.offsetTop - 125;
				}, 300);
			}
		});

		document.body.addEventListener('mouseover', function(event){
			if(tocEl.classList.contains("show")){
				tocEl.classList.remove("show");
			}
		});

		var changeLocation = function(event,el){
			event.preventDefault();
			var id = el.getAttribute('data-id');
			var part = el.getAttribute('data-part');
			var hash = window.location.hash.substring(1);
			if(id!=undefined)
				part += '#'+id;

			window.location.href = PATH+part;
			if(hash==id){
				var event = document.createEvent("HTMLEvents");
				event.initEvent("hashchange", true, false);
				document.body.dispatchEvent(event);
			}
		};

		document.getElementById('ariadne').addEventListener('click', function(event){
			if(event.target && event.target.nodeName == "SPAN")
				changeLocation(event,event.target);
		});

		tocEl.addEventListener('click', function(event){
			if(event.target && event.target.parentNode.nodeName == "LI")
				changeLocation(event,event.target.parentNode);
		});

		var validTag = function(el, tag) {
			console.log(tag,el.nodeName);
			if(el.nodeName === tag)
				return el;
			while (el.parentNode) {
					el = el.parentNode;
					console.log(el.tagName);
					if (el.tagName === tag)
							return el;
			}
			return null;
		};

		// link / crossRef
		contentTEI.addEventListener('click', function(event){
			if(event.target) {
				var el = validTag(event.target,refTEIElU);
				if(el!=null){
					event.preventDefault();
					var id = el.getAttribute('target');
					if(id.lastIndexOf(".")>-1){
						window.location.href = id;
					} else {
						var part = el.getAttribute('creferencing');
						var rendition = el.getAttribute('rendition');

						if(rendition!=undefined && rendition.match(/notecall/)){
							_selectFootnote(id.substr(1));
						} else {
							if(/#\d{13}:/.test(id)) {
								$getJSON({
									url : 'index.php',
									data : {
										module : 'Search',
										action : 'getCrossDoc',
										id : id,
										book : BOOK,
										part : PART
									},
									success : function(data){
										window.location.href = PATH+data.book+'/'+data.part+'#'+data.anchor;
									}
								});
							} else if(id.lastIndexOf("#")>-1) {
								window.location.href = PATH+part+id;
							} else {
								window.location.href = PATH+part+'#'+id;
							}
						}
					}
				}
			}
		});

		// show citations button & citation page
		contentTEI.addEventListener("mouseup", function(event) {
			var selection = window.getSelection();
			if(!selection.isCollapsed){
				var node = selection.anchorNode,
				startNode = (node && node.nodeType === 3 ? node.parentNode : node);
				var boundary = selection.getRangeAt(0).getClientRects();
				boundary = boundary[0];
				var top  = window.pageYOffset || document.documentElement.scrollTop;
				citationsEl.style.top = ( (boundary.top+top)-10 ) + 'px';
				var teiRects = document.getElementById('tei').getClientRects();
				teiRects = teiRects[0];
				var left = (document.getElementById('tei').offsetLeft+teiRects.width)-50;
				citationsEl.style.left = left + 'px';
				citationsEl.style.display = 'block';
			}  else {
				citationsEl.style.display = 'none';
				var nodeName = event.target.nodeName.toLowerCase();
				if(nodeName==pageTEIEl){
					var attrN = event.target.getAttribute(ELS.pb.n);
					if(attrN!=undefined){
						var temoin = event.target.getAttribute(ELS.pb.ed);
						if(temoin==undefined || temoin!='temoin'){
							$citation.insert({
								zord_type : 'page',
								page : attrN,
								book : BOOK,
								zord_url : PATH+BOOK+'/'+PART+'#'+event.target.id
							});
						}
					}
				}
			}
		});

		// citations button
		document.getElementById('tool_citation').addEventListener("click", function(event) {
			$citation.insert({
				zord_type : 'citation',
				zord_citation : '',
				page : null,
				book : BOOK,
				zord_url : PATH+BOOK
			});
		});
		var insertCitation = function(type){
			var selection = window.getSelection();
			if(!selection.isCollapsed){
				var html = '';
				if (selection.rangeCount) {
					var container = document.createElement('div');
					for (i = 0, len = selection.rangeCount; i < len; i += 1) {
						container.appendChild(selection.getRangeAt(i).cloneContents());
					}
					html = container.innerHTML;
					// get pages number and id
					var top = selection.getRangeAt(0).getBoundingClientRect().top+$scrollTop.get();
					var page = 0;
					var id = null;
					[].forEach.call(contentTEI.querySelectorAll(pageSelector), function (el,i) {
						var temoin = el.getAttribute(ELS.pb.ed);
						if(temoin==undefined || temoin!='temoin'){
							if(el.offsetTop<=top){
								page = el.getAttribute(ELS.pb.n);
								id = el.id;
							}
						}
					});
					$citation.insert({
						zord_type : type,
						zord_citation : html,
						page : page,
						book : BOOK,
						zord_url : PATH+BOOK+'/'+PART+'#'+id
					});
				}
			} else {
				$citation.noSelect(type);
			}
			citationsEl.style.display = 'none';
			selection.removeAllRanges();
		};
		document.getElementById('citationsButton').addEventListener("click", function() {
			insertCitation('citation');
		});
		document.getElementById('tool_bug').addEventListener('click',function(){
			insertCitation('bug');
		});

		// search in book
		document.getElementById('tocSearchQuery').addEventListener("keyup", function(event) {
			if(event.keyCode==13){
				if(document.getElementById('tocSearchQuery').checkValidity()){
					event.preventDefault();
					event.stopPropagation();
					sessionStorage.setItem("searchInBook", JSON.stringify({book:BOOK,value:this.value}));
					document.location.href = PATH+"page/search";
				}
			}
		});
	});
})();
