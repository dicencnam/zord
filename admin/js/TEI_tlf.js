/**
 * teiSpliter portal tlf
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
(function(undefined) {

	if (!Date.prototype.isValid) { // true
		// http://stackoverflow.com/questions/1353684/detecting-an-invalid-date-date-instance-in-javascript
		Date.prototype.isValid = function () {
			// An invalid date object returns NaN for getTime() and NaN is the only
			// object not strictly equal to itself.
			return this.getTime() === this.getTime();
		};
	}

	var sectionCount = 0;
	var structureCount = 1;
	var errors = {
		noHead : []
	};

	var getTitle = function(element){
		var tempTitle = document.createElement('div');
		var headEl = element.querySelector('tei\\:head');
		if(headEl!=null){
			tempTitle.insertAdjacentHTML('beforeend',headEl.innerHTML);
			[].forEach.call(tempTitle.querySelectorAll('*'), function (el) {
				var nodeName = el.nodeName.toLowerCase();
				if(nodeName=='tei:note' || nodeName=='tei:ref')
					el.parentNode.removeChild(el);
			});
		} else {
			var id = element.getAttribute('xml:id');
			errors.noHead.push(id);
			return '--------------------- NO TITLE ---------------------';
		}
		return tempTitle.textContent.replace(/\s{2,}/g,' ');
	};

	var creatID = function(){
		sectionCount++;
		return 'Zsec_'+sectionCount;
	};

	var setSection = function(partName,xmlID,element){
		var html = [];
		[].forEach.call(element.querySelectorAll('tei\\:div[xml\\:id="'+xmlID+'"] > tei\\:div'), function (el,i) {
			var type = el.getAttribute('type');
			if(type=='section' || type=='scene' || type=='letter'){
				var id = el.getAttribute('xml:id');
				if(id==undefined){
					id = creatID();
					sectionCount++;
					el.setAttribute('xml:id',id);
				}
				html.push('<li data-part="'+partName+'" data-id="'+id+'"><span>'+getTitle(el)+'</span>'+setSection(partName,id,el)+'</li>');
			}
		});

		if(html.length>0)
			return '<ul>'+html.join('')+'</ul>';
		return html;
	};

	var creatTempDir = function(contentEl){
		var tempTEI = document.createElement('div');
		var teiString = '<tei:tei';
		[].forEach.call(contentEl.querySelector('tei\\:tei').attributes, function(att) {
			teiString += ' '+att.nodeName+'='+att.value;
		});
		teiString +='></tei:tei>';
		tempTEI.insertAdjacentHTML('beforeend',teiString);
		tempTEI = tempTEI.querySelector('tei\\:tei');
		return tempTEI;
	};

	var getHeader = function(contentEl){
		var headerEl = contentEl.querySelector('tei\\:teiHeader');
		var headerString = (new XMLSerializer()).serializeToString(headerEl)
			.replace(' xmlns="http://www.w3.org/1999/xhtml"','').replace(/tei:/g,'');
		headerEl.parentNode.removeChild(headerEl);
		return headerString;
	};
/*
	var getCategory = function(contentEl){
		var seriesStmt = contentEl.querySelector('tei\\:seriesStmt');
		if(seriesStmt){
			var id = seriesStmt.getAttribute('xml:id');
			if(id)
				return id;
		}
		return null;
	};
*/

	var getAbstract = function(contentEl){
		var abstractEl = contentEl.querySelector('tei\\:front > tei\\:argument');
		if(abstractEl)
			return abstractEl.textContent.replace(/\s+/g,' ');
		return '';
	};

	var getStructure = function(contentEl){
		var titlePage = contentEl.querySelector('tei\\:front > tei\\:titlePage');
		if(titlePage){
			var tiltePageID = creatID();
			titlePage.setAttribute('xml:id',tiltePageID)
			return {
				0 : {partName:'home','element':titlePage,level:0,part:'titlePage',id:tiltePageID,'title':'home'}
			};
		} else {
			return null;
		}
	};

	var createNavigate = function(fileName,structure){
		var TocRoot = document.createElement('div');
		TocRoot.insertAdjacentHTML('beforeend','<ul><li class="tocFront" data-part="home" data-id="Zsec_1"><span class="title-front"></span><ul id="tocTEI_front"></ul></li></ul><ul><li class="tocBody"><span class="title-body"></span><ul id="tocTEI_body"></ul></li></ul><ul><li class="tocBack"><span class="title-back"></span><ul id="tocTEI_back"></ul></li></ul>');
		var oldID = tocElem = null;
		var oldLevel = 1;
		var levels = {};
		var ids = {};
		var idStructure = function(element,partName){
			[].forEach.call(element.querySelectorAll('*[xml\\:id]'), function (el) {
				var v = el.getAttribute('xml:id');
				ids[v] = partName;
			});
		};
		for(var item in structure){
			var el = structure[item]['element'];
			var level = structure[item]['level'];
			var partName = structure[item]['partName'];
			var xmlID = structure[item]['id'];

			idStructure(el,partName);
			if(partName!='home'){
				levels[level] = partName;
				if(level==1){
					tocElem = TocRoot.querySelector('#tocTEI_'+structure[item]['part']);
				} else if(level>oldLevel){// 2-3-4 <-???
					tocElem = TocRoot.querySelector('li[data-part="'+levels[level-1]+'"] > ul');
					if(tocElem===null){
						TocRoot.querySelector('li[data-part="'+levels[level-1]+'"]').insertAdjacentHTML('beforeend','<ul></ul>');
						tocElem = TocRoot.querySelector('li[data-part="'+levels[level-1]+'"] > ul');
					}
				} else {
					tocElem = TocRoot.querySelector('li[data-part="'+levels[level-1]+'"] > ul');
				}
				structure[item]['title'] = getTitle(el);
				tocElem.insertAdjacentHTML('beforeend','<li data-part="'+partName+'" data-id="'+xmlID+'"><span>'+structure[item]['title']+'</span>'+setSection(partName,xmlID,el)+'</li>');
				oldLevel = level;
			}
		}

		[].forEach.call(TocRoot.querySelectorAll('*[data-part]'), function (el) {
			var v = el.getAttribute('data-part');
			if(v=='home')
				el.setAttribute('data-part',fileName);
			else
				el.setAttribute('data-part',fileName+'/'+v);
		});
		var toc = TocRoot.innerHTML;
		return {ids:ids,toc:toc};
	};

	var referencing = function(fileName,contentEl,ids){
		var errorRef = [];
		var protocols = ['http','https','ftp','ftps'];
		[].forEach.call(contentEl.querySelectorAll('*[target]'), function (el) {
			var v = el.getAttribute('target');
			var vr = v.slice(1);
			if(ids[vr]!=undefined){
				el.setAttribute('cReferencing',fileName+'/'+ids[vr]);
			} else {
				if(v.lastIndexOf(".")==-1 && !/#\d{13}:/.test(v))
					errorRef.push(v);
			}
		});
		return errorRef;
	};


	var checkPages = {
		startPage : 0,
		errorsPage : [],
		oldPage : '',
		check : function(contentEl) {
			var t = this;
			var seq = [];
			t.startPage = 0;
			t.errorsPage = [];
			t.oldPage = '';
			var toDecimal = function(str) {
				var decimal = 0;
				var letters = str.split(new RegExp());
				for(var i = letters.length - 1; i >= 0; i--) {
					decimal += (letters[i].charCodeAt(0) - 64) * (Math.pow(26, letters.length - (i + 1)));
				}
				return decimal-32;
			};
			[].forEach.call(contentEl.querySelectorAll('tei\\:pb[n]'), function (el) {
				var ed = el.getAttribute('ed');
				if(ed==undefined)
					seq.push(el.getAttribute('n'));
			});
			seq.forEach(function(page,i){
				if(!isNaN(page)){// arabic
					var p = page*1;
				} else {
					var p = t.romanConvert(page.toUpperCase());
				}

				if(p>0 && page.lastIndexOf("-")==-1){
					splitCount = 0;
					if(i==0){
						t.startPage = p;
					} else {
						if(p!=t.startPage+1 && p!=1){
							t.errorsPage.push('inavlid seq:'+t.oldPage+'→'+page);
						}
						t.startPage = p;
					}
				} else {
					var split = page.split('-');
					if(split.length>1 && split[0]!=''){
						if(!isNaN(split[1]))
							split[1] = split[1]*1;
						else{
							split[1] = toDecimal(split[1]);
						}

						if(typeof splitCount!='undefined' && split[1]==splitCount+1)
								splitCount = split[1];
						else
							t.errorsPage.push('inavlid number:'+page);
					} else {
						t.errorsPage.push('inavlid number:'+page);
					}
				}
				t.oldPage = page;
			});
			return t.errorsPage;
		},
		romanValues : [['M',1000], ['CM',900], ['D',500], ['CD',400],['C',100], ['XC',90], ['L',50],['XL',40],['X',10], ['IX',9], ['V',5],['IV',4],['I',1]],

		romanConvert : function(str) {
			var result = 0
			for (var i=0; i<this.romanValues.length; ++i) {
				var pair = this.romanValues[i]
				var key = pair[0]
				var value = pair[1]
				var regex = RegExp('^' + key)
				while (str.match(regex)) {
					result += value
					str = str.replace(regex, '')
				}
			}
			return result
		}
	};

	var checkGraphics = {
		check : function(contentEl,graphics) {
			var errorGraphics = [];
			[].forEach.call(contentEl.querySelectorAll('tei\\:graphic'), function (el,i) {
				var url = el.getAttribute('url');
				if(url!=undefined){
					url = url.split('/').pop();
					if(graphics.indexOf(url) === -1)
						errorGraphics.push({pos:i+1,url:url});
				}
			});
			return errorGraphics;
		}
	};

	var checkDates = {
		check : function(contentEl) {
			var dates = [];
			[].forEach.call(contentEl.querySelectorAll('tei\\:date'), function (el) {
				var when = el.getAttribute('when');
				var notBefore = el.getAttribute('notBefore');
				var notAfter = el.getAttribute('notAfter');
				if(when){
					var d = new Date(when);
					if(!d.isValid())
						dates.push(when);
				}
				if(notBefore){
					var d = new Date(notBefore);
					if(!d.isValid())
						dates.push(notBefore);
				}
				if(notAfter){
					var d = new Date(notAfter);
					if(!d.isValid())
						dates.push(notAfter);
				}
			});
			return dates;
		}
	};

	var floatingText = {
		check : function(contentEl) {
			var t = contentEl.querySelectorAll('tei\\:floatingText');
			return t.length;
		}
	};

	var lbracket = {
		check : function(contentEl) {
			var count = 0;
			var txt = '';
			[].forEach.call(contentEl.querySelectorAll('tei\\:l'), function (el) {
				for (var i = 0; i < el.childNodes.length; ++i){
					if (el.childNodes[i].nodeType === 3){
						var str = el.childNodes[i].textContent.replace(/^\s+/gm,'');
						if(str.indexOf("[") == 0){
							count++;
							txt += str+"\n";
						}
					}
				}
			});
			return {
				count : count,
				txt : txt
			};
		}
	};

	var getContentType = function(el){
		var type_el = el.getAttribute('type');
		if(type_el=='index' || type_el=='bibliography' || type_el=='glossary' || type_el=='toc')
			return 1;
		return 0;
	};

	var parse = function(fileName,tei,graphics){
		var contentEl = document.createElement('div');
		contentEl.innerHTML = tei;
		tei = null;
		var refs = {
			toc : null
		};
		errors = {
			noHead : [],
			pages : [],
			dates : [],
			graphics : [],
			floatingText : 0,
			lbracket : 0
		};
		sectionCount = 0;
		count = 1;

		errors.pages = checkPages.check(contentEl);

		errors.graphics = checkGraphics.check(contentEl,graphics);

		errors.dates = checkDates.check(contentEl);

		errors.floatingText = floatingText.check(contentEl);

		errors.lbracket = lbracket.check(contentEl);

		// var category = getCategory(contentEl);

		var headerString = getHeader(contentEl);

		var structure = getStructure(contentEl);

		if(structure!=null){
			var abstract = getAbstract(contentEl);

			var scan = function(element,part,selector,level){
				[].forEach.call(element.querySelectorAll(selector), function (el,i) {
					var type = el.getAttribute('type');
					var partName = part+'-'+(i+1);
					var id = el.getAttribute('xml:id');
					if(id==undefined){
						id = creatID();
						sectionCount++;
						el.setAttribute('xml:id',id);
					}
					structure[structureCount] = {partName:partName,'element':el,level:level,part:part,id:id};
					structureCount++;
					if(type!=undefined && type=="part")
						scan(el,partName,'tei\\:div[xml\\:id="'+id+'"] > tei\\:div',level+1);
				});
			};

			['front','body','back'].forEach(function(part){
				scan(contentEl,part,'tei\\:'+part+' > tei\\:div',1);
			});

			refs = createNavigate(fileName,structure);
			var ids = refs.ids;

			var errorRef = referencing(fileName,contentEl,ids);

			// clear structure
			var tempTEI = creatTempDir(contentEl);
			for(var item in structure){
				tempTEI.innerHTML = '';
				var el = structure[item]['element'].cloneNode(true);
				var partName = structure[item]['partName'];
				var id = structure[item]['id'];
				structure[item]['contentType'] = getContentType(el);

				tempTEI.appendChild(el);
				var level = structure[item]['level'];
				var query = 'tei\\:div[xml\\:id="'+id+'"] > tei\\:div';
				[].forEach.call(tempTEI.querySelectorAll(query), function (el) {
					var type = el.getAttribute('type');
					if(type!='section' && type!='letter' && type!='scene')
						el.parentNode.removeChild(el);
				});
				structure[item]['element'] = (new XMLSerializer()).serializeToString(tempTEI)
					.replace(' xmlns="http://www.w3.org/1999/xhtml"','').replace(/xml:id=/g,'id=');
			}
		}

		return {
			structure : structure,
			header : headerString,
			toc : refs.toc,
			errorRef : errorRef,
			ids: ids,
			// category : category,
			noHead : errors.noHead,
			pages : errors.pages,
			dates : errors.dates,
			graphics : errors.graphics,
			floatingText : errors.floatingText,
			lbracket : errors.lbracket,
			abstract : abstract
		}
	};

	$notify.sub('teiParse:tlf', function(fileName,tei,graphics,callback){
		callback(parse(fileName,tei,graphics));
	});

	$notify.sub('teiSave:tlf',function(repository,fileName,level,headerString,structure,toc,abstract,ids,callback){
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_TEI_tlf',
				action : 'saveSource',
				structure : JSON.stringify(structure),
				ids : JSON.stringify(ids),
				header : headerString,
				toc : toc,
				abstract : abstract,
				fileName : fileName,
				level : level,
				repository : repository
			},
			success : function (data) {
				if(callback!=undefined)
					callback();
			},
			error : function (code,data) {
				MSG.alert('Error',data.message);
			}
		});
	});

	$notify.sub('teiChangeLevel:tlf',function(id,repository,level,callback){
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_TEI_tlf',
				action : 'updateLevelSource',
				fileName : id,
				repository : repository,
				level : level
			},
			success : function (data) {
				if(callback!=undefined)
					callback();
			}
		});
	});

	$notify.sub('teiChangeNovelty:tlf',function(id,repository,novelty,callback){
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_TEI_tlf',
				action : 'updateNovelty',
				fileName : id,
				repository : repository,
				novelty : novelty
			},
			success : function (data) {
				if(callback!=undefined)
					callback();
			}
		});
	});

	return parse;
})();
