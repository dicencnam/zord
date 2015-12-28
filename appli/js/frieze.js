
window.$frieze = (function(undefined) {
var initGroup = function(prefix,msgTable,msgOcc,data){
	var name = data.name;
	var occurrencesEl = document.getElementById(prefix+'Occ_'+name);

	var occHeadEl = document.getElementById(prefix+'OccHead_'+name);
	var frameMsgEl = document.getElementById(prefix+'FrameMsg_'+name);

	return  {

		msg : function(count){
			occHeadEl.style.display = 'none';
			msgTable(frameMsgEl,count);
		},

		occMsg : function(count){
			if(count.occ!=0)
				occHeadEl.style.display = 'table';
			else
				occHeadEl.style.display = 'none';

			msgOcc(frameMsgEl,count);
		},

		tableSet : function(docs,pos){
			if(occurrencesEl){
			var table = new window.TableHTML(data.tableTPL);
			occurrencesEl.style.display = 'none';

			if(data.type=='source'){
				if(docs[pos]!=undefined){
					docs[pos].forEach(function(data){
						table.set(data);
					});
				}
			} else {
				docs.forEach(function(data){
					table.set(data);
				});
			}
			// ie9 bug
			var head = document.getElementById(prefix+'Head_'+name).innerHTML;
			var parent = document.getElementById(prefix+'_'+name).parentNode;
			parent.innerHTML = '<table id="'+prefix+'_'+name+'"><thead id="'+prefix+'Head_'+name+'">'+head+'</thead><tbody>'+table.get()+'</tbody></table>';


			var tableEl = document.getElementById(prefix+'_'+name);
			tableEl.style.display = 'table';

			this.msg(table.getCounter());}
		},
		occSet : function(docs,search,pos){
			document.getElementById(prefix+'_'+name).style.display = 'none';
			occurrencesEl.style.display = 'block';

			var OccHTML = new window.OccHTML(data.occTPL);
			var result = '<p class="no-result">'+LOCALES.noresult+'</p>';

			var Occs = {};

			if(data.type=='source'){
				if(search.response.numFound>0 && docs[pos]!=undefined){

					docs[pos].forEach(function(doc,i){
						OccHTML.createSection(doc);
						OccHTML.setTitle(doc);
						var occCount = 0;
						search.response.docs.forEach(function(d,i){
							if(doc.book_s == d.book_s+''){
								occCount += OccHTML.setOcc(d,search.highlighting);
								Occs[doc.book_s] = occCount;
							}
						});
						OccHTML.closeSection();
					});
					result = OccHTML.get();

					occHeadEl.style.display = 'table';
				} else {
					occHeadEl.style.display = 'none';
				}
			} else {
				if(search.response.numFound>0 && docs.length>0){
					docs.forEach(function(doc){
						OccHTML.createSection(doc);
						OccHTML.setTitle(doc);
						var occCount = 0;
						search.response.docs.forEach(function(d,i){
							if(doc.book_s == d.book_s+''){
								occCount += OccHTML.setOcc(d,search.highlighting);
								Occs[doc.book_s] = occCount;
							}
						});

						OccHTML.closeSection();
					});
					result = OccHTML.get();

					occHeadEl.style.display = 'table';
				} else {
					occHeadEl.style.display = 'none';
				}
			}
			occurrencesEl.innerHTML = result;
			this.occMsg(OccHTML.getCounters());
			return Occs;
		}
	}
};

var inCategories = function(){
	return function(categ){
		if(this.categories.indexOf(categ)>-1)
			return true;
		return false;
	};
};

var getGroupName = function(data){
	var length = data.length;
	return function(categ){
		var name = '';
		for (var i = 0; i < length; ++i) {
				if(data[i].categories.indexOf(categ)>-1){
					name = data[i].name;
					break;
				}
		}
		return name;
	};
};

var setInitGroup = function(data){
	var length = data.length;
	return function(){
		for (var i = 0; i < length; ++i){
			if(this.actions[data[i].name]=='source')
				this.actions[data[i].name].group = {};
			else
				this.actions[data[i].name].group = [];
		}
	};
};

var renderGroup = function(data){
	var length = data.length;
	return function(type,search,pos){
		var OccCount = [];
		for (var i = 0; i < length; ++i){
			if(type=='table')
				this.actions[data[i].name].tableSet(this.actions[data[i].name].group,pos);
			else{
				var occGp = this.actions[data[i].name].occSet(this.actions[data[i].name].group,search,pos);
				for (var book in occGp) {
					OccCount[book] = occGp[book];
				}
			}
		}
		return OccCount;
	};
};

var pushInGroup = function(){
	return function(doc,posBlock){
		var name = this.getGroupName(doc.category_ss[0]); // TODO: categ !!!!
		if(name==''){
			console.log('ERROR category ('+doc.book_s+'):'+doc.category_ss[0]);
		} else {
			if(this.type=='source'){
				if(this.actions[name].group[posBlock] == undefined)
					this.actions[name].group[posBlock] = [];
				this.actions[name].group[posBlock].push(doc);
			} else {
				this.actions[name].group.push(doc);
			}
		}
	};
};

var frieze = {
	step: 0,
	booksOcc: null,
	Occs: null,
	init : function(def){
		var _this = this;
		// assign vars
		this.def = def;
		// initGroup

		this.groupSource = {
			type : "source",
			data : [],
			categories : [],
			actions : {},
			pushInGroup : pushInGroup()
		};

		this.groupNoSource = {
			type : "nosource",
			categories : [],
			data : [],
			actions : {},
			pushInGroup : pushInGroup()
		};

		this.def.defineFrame.forEach(function(data){
			if(data.type=='source'){
				_this.groupSource.categories = _this.groupSource.categories.concat(data.categories);
				_this.groupSource.data.push({name : data.name,categories : data.categories});
				_this.groupSource.actions[data.name] = initGroup(_this.def.prefix,_this.def.msgTable,_this.def.msgOcc,data);
			} else {
				_this.groupNoSource.categories = _this.groupNoSource.categories.concat(data.categories);
				_this.groupNoSource.data.push({name : data.name,categories : data.categories});
				_this.groupNoSource.actions[data.name] = initGroup(_this.def.prefix,_this.def.msgTable,_this.def.msgOcc,data);
			}
		});

		_this.groupSource.getGroupName = getGroupName(_this.groupSource.data);
		_this.groupSource.setInitGroup = setInitGroup(_this.groupSource.data);
		_this.groupSource.render = renderGroup(_this.groupSource.data);
		_this.groupSource.inCategories = inCategories();
		_this.groupNoSource.getGroupName = getGroupName(_this.groupNoSource.data);
		_this.groupNoSource.setInitGroup = setInitGroup(_this.groupNoSource.data);
		_this.groupNoSource.render = renderGroup(_this.groupNoSource.data);
		_this.groupNoSource.inCategories = inCategories();

		if(this.def.bars!=undefined){
			this.bars = this.def.bars;
			this.steps = this.def.steps;
			// init html
			var marginMark = (this.bars.height/2)-12;
			this.bars.element.style.height = (this.bars.height+70)+'px';
			this.bars.element.innerHTML = [
			'<div id="friezeBarTop" style="width:'+(this.bars.width+50)+'px;">▲</div>',
			'<div id="friezeBarLeft" style="height:'+this.bars.height+'px;" data-display="false">',
				'<span style="margin-top:'+marginMark+'px;">❮</span>',
			'</div>',
			'<div id="friezeWarp" style="width:'+this.bars.width+'px;"><div id="friezeSlider"></div></div>',
			'<div id="friezeBarRight" style="height:'+this.bars.height+'px;" data-display="false">',
				'<span style="margin-top:'+marginMark+'px;">❯</span>',
			'</div>'
			].join('');

			// assign elements
			this.bars.friezeWarp = document.getElementById('friezeWarp');
			this.bars.friezeSlider = document.getElementById('friezeSlider');
			this.bars.leftEl = document.getElementById('friezeBarLeft');
			this.bars.rightEl = document.getElementById('friezeBarRight');
			this.bars.topEl = document.getElementById('friezeBarTop');

			// events
			this.bars.leftEl.addEventListener('click', function() {
				if(this.getAttribute('data-display')=='true')
					_this.toSlide(_this.posStart-1)
			});
			this.bars.rightEl.addEventListener('click', function() {
				if(this.getAttribute('data-display')=='true')
					_this.toSlide(_this.posStart+1)
			});
			this.bars.friezeSlider.addEventListener('click', function(event) {
				if(event.target && event.target.nodeName == 'DIV'){
					var bar = event.target.getAttribute('data-bar');
					if(bar!=undefined){
						var start = _this.def.start+(bar*_this.steps[_this.step].increment);
						var nStep = _this.step+1;
						if(_this.steps[nStep]!=undefined){
							_this.step = nStep;
							if(_this.step>0)
								_this.bars.topEl.style.visibility = 'visible';
							frieze.parse(start,_this.steps[_this.step].bars,_this.steps[_this.step].increment);
						}
					}
				}
			});
			this.bars.topEl.addEventListener('click', function(event) {
				if(_this.step>0){
					var date = _this.def.start+(_this.posStart*(_this.steps[_this.step].bars*_this.steps[_this.step].increment));
					_this.step--;
					if(_this.step==0)
						this.style.visibility = 'hidden';
					frieze.parse(date,_this.steps[_this.step].bars,_this.steps[_this.step].increment);
				}
			});
		} else {
			this.def.start = -5000;
			this.def.end = 5000;
			this.steps = [{bars:10000,increment:1}];
		}
	},

	start :function(){
		this.parse(this.def.start,this.steps[0].bars,this.steps[0].increment);
	},

	toSlide :function(pos){
		var _this = this;
		this.posStart = pos;
		var html = [];
		var before = 'false';
		var after = 'false';
		if(pos>0)
			before = 'true';
		if(pos < this.barsBlock-1)
			after = 'true';

		// right/left navigation
		this.bars.leftEl.setAttribute('data-display',before);
		this.bars.rightEl.setAttribute('data-display',after);
		this.bars.friezeSlider.style.left = -((pos*this.barsWidth)+1) + 'px';
		setTimeout(function(){
			_this.bars.friezeSlider.removeAttribute('data-transition');
		},200);

		if(_this.booksOcc==null){
			_this.groupSource.render('table',null,pos);
		} else {
			var occCount = _this.groupSource.render('occ',_this.search,pos);
			for (var book in _this.Occs) {
				if(occCount[book]!=undefined)
					_this.Occs[book] = occCount[book];
			}
		}
	},
	getPosFromDate : function(date){
		return Math.floor(((date - this.def.start)/this.steps[this.step].increment)/this.steps[this.step].bars);
	},
	parse : function(start,bars,increment){
		var _this = this;
		var length = Math.ceil((this.def.end - this.def.start)/increment);
		this.barsBlock = Math.ceil(length/bars);
		var barsCount = [].slice.apply(new Uint8Array(this.barsBlock*bars));

		_this.groupSource.setInitGroup();
		_this.groupNoSource.setInitGroup();

		var docsOccs = [];
		if(_this.booksOcc!=null){
			docsOccs = [].slice.apply(new Uint8Array(this.barsBlock*bars));
			this.docsOccsID = {};
		}

		for(var date in this.def.documents){
			if(date>=this.def.start && date<=this.def.end){
				var pos = Math.floor((date-this.def.start)/increment);

				var posBlock = this.getPosFromDate(date);

				var count = 0;
				this.def.documents[date].forEach(function(doc){

					if(_this.groupSource.inCategories(doc.category_ss[0])){
						if(_this.booksOcc!=null){
							if(_this.booksOcc.indexOf(doc.book_s)>-1){
								if(_this.docsOccsID[posBlock] == undefined)
									_this.docsOccsID[posBlock] = [];
								_this.groupSource.pushInGroup(doc,posBlock);
								_this.docsOccsID[posBlock].push(doc.book_s);;
								if(docsOccs[pos]==0)
									docsOccs[pos] = [];
								docsOccs[pos].push(doc.book_s);
								count++;
							}
						} else {
							_this.groupSource.pushInGroup(doc,posBlock);
							count++;
						}
					} else {
						if(_this.booksOcc!=null){
							if(_this.booksOcc.indexOf(doc.book_s)>-1){
								_this.groupNoSource.pushInGroup(doc);
							}
						} else {
							_this.groupNoSource.pushInGroup(doc);
						}
					}
				});

				barsCount[pos] = barsCount[pos]+count;
			} else {// nosource
				this.def.documents[date].forEach(function(doc){
					if(_this.booksOcc!=null){
						if(_this.booksOcc.indexOf(doc.book_s)>-1){
							_this.groupNoSource.pushInGroup(doc);
						}
					} else {
						_this.groupNoSource.pushInGroup(doc);
					}
				});
			}
		}
		if(this.bars!=undefined){
			var posStart = this.getPosFromDate(start);
			if(start == this.def.start && _this.booksOcc!=null){
					for (var i = 0; i < barsCount.length; i++) {
						if(barsCount[i]>0){
							posStart = Math.floor(i/increment);
							break;
						}
					}
			}
			this.toSlide(posStart);

			var htmlBar = ['<div class="friezeBar"><div style="width:0px;height:'+this.bars.height+'px;"></div>'];
			var htmlDate = ['<div class="friezeDate">'];
			var height = Math.floor(this.bars.height/Math.max.apply( Math, barsCount ));
			var width = Math.floor(this.bars.width/this.steps[this.step].bars);
			this.barsWidth = (width+1)*this.steps[this.step].bars;
			this.bars.friezeWarp.style.width = (this.barsWidth-1)+'px';
			this.bars.element.style.width = (this.barsWidth+50)+'px';
			this.bars.topEl.style.width = (this.barsWidth+50)+'px';
			if(docsOccs){
				docsOccs.forEach(function(value,i){
					if(typeof value =='object'){
						var _dd = 0;
						value.forEach(function(v){_dd += _this.Occs[v];});
						docsOccs[i] = _dd;
					}
				});
			}
			barsCount.forEach(function(value,i){
				var occ = '';
				if(docsOccs && docsOccs[i]>0)
					occ = '<span class="bar_occ">'+docsOccs[i]+'</span>';
				htmlBar.push('<div style="width:'+width+'px;height:'+(value*height)+'px" data-bar="'+i+'">'+occ+'<span class="bar_book">'+value+'</span></div>');
				htmlDate.push('<div style="width:'+width+'px;"><span>'+(_this.def.start+(increment*i))+'</span></div>');
			});
			htmlBar.push('</div>');
			htmlDate.push('</div>');
			this.bars.friezeSlider.innerHTML = htmlBar.join('')+htmlDate.join('');
			this.bars.friezeSlider.setAttribute('data-transition','none');
		} else {
			if(_this.booksOcc==null){
				_this.groupSource.render('table',null,0);
			} else {
				var occCount = _this.groupSource.render('occ',_this.search,pos);
				for (var book in _this.Occs) {
					if(occCount[book]!=undefined)
						_this.Occs[book] = occCount[book];
				}
			}
		}

		// ----------------------------------------------------------------------
		// noSources
		if(_this.booksOcc!=null){
			_this.groupNoSource.render('occ',this.search);
		} else {
			_this.groupNoSource.render('table');
		}
	},

	occurrences : function(data){
		this.step = 0
		this.search = data;
		this.booksOcc = [];
		this.categsOcc = [];
		this.Occs = {};
		for(var name in data.highlighting){
			var id = name.split('_')[0];
			if(this.Occs[id]==undefined){
				this.Occs[id] = 0;
				this.booksOcc.push(id);
			}
		}
		if(this.bars!=undefined)
			this.bars.topEl.style.visibility = 'hidden';
		this.parse(this.def.start,this.steps[0].bars,this.steps[0].increment);
	},

	refrech : function(){
		this.categsOcc = null;
		this.booksOcc = null;
		this.Occs = null;
		this.parse(this.def.start,this.steps[0].bars,this.steps[0].increment);
	}
};

return frieze;
})();
