
RegExp.escape = function( value ) {
	return value.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&");
}

window.OccHTML = (function(undefined) {
	var OccHTML = function(model){
		var dates = ['creation_date_i','creation_date_after_i','date_i'];
		var persons = ['creator_ss','editor_ss'];
		var seqTitleTPL  = model.tpl;
		var html = {};
		var regItem = /<b>(.*?)<\/b>/ig;
		var occCount = 0;
		var bookCount = 0;
		var chapter = null;

		var _setTitle = function (line){
			html[chapter].title = '<header><h1>'+line.join('')+'</h1></header>';
			bookCount++;
		};

		var _getCell = function(cell,data){
			return '<span>'+data[cell]+'</span>';
		};

		var _getDate = function(cell,data){
			var date = '';
			if(data[cell]!=undefined)
				date = data[cell];
			return '<span class="'+cell+'_Occ">'+date+'</span>';
		};

		var _getTitle = function(data){
			var subtitle = '';
			if(data['subtitle_s']!=undefined)
				subtitle = '. '+data['subtitle_s'];
			var title = '';
			if(data['title_s']!=undefined)
				title = data['title_s'];
			return '<span class="title_s_Occ"><a href="'+PATH+data['book_s']+'">'+title+subtitle+'</a></span>';
		};

		var _getPersons = function(cell,data){
			var persons = '';
			if(data[cell]!=undefined){
				var etAl = false;
				var _p = data[cell];
				if(_p.length>3){
					_p = _p.slice(0,3);
					etAl = true;
				}
				persons = _p.join('&#160;; ');
				if(etAl)
					persons += ', <i>et al.</i>';
			}
			return '<span class="'+cell+'_Occ">'+persons+'</span>';
		};

		return {
			/**
			* Insert occurences
			* @method setOcc
			* @param {object} doc document
			* @param {object} data Solr highlighting object
			*/
			setOcc : function(doc,highlighting){
				var items = [];
				var count = 0;
				var max = 20;
				var occLength = 0;
				var oldString = '';
				if(highlighting[doc.id].content.length>0){
						highlighting[doc.id].content.forEach(function(item,i, arr){
								item = item.replace(/<\/b> <b>/g,' ').replace(/\s+/g,' ');
								var found = item.match(regItem);
								occLength += found.length;
								if(occLength<=max){
									found.forEach(function(el,i){
											count++;
											if(i>0)
												item = item.replace(/<b>/,'').replace(/<\/b>/,'');

											var exp = new RegExp(RegExp.escape(el),"i");
											item.replace(exp, function(match, offset, string){
												var matchC = match.replace(/<\/*b>/g,'');
												var first = '<span class="left">'+string.substr(0,offset).replace(/<\/*b>/g,'')+'</span>';
												var second = '<span class="right">'+match+string.substr(offset+match.length,string.length).replace(/<\/*b>/g,'')+'</span>';
												found[i] = '<div class="snip" data-match="'+matchC+'" data-file="'+doc.file_s+'" data-mark="'+count+'">'+first+second+'<div class="tooltip">'+first+second+'</div></div>';
											});
									});
									items.push(found.join(''));
								}
						});
					occCount += occLength;
					if(occLength>max)
						items.push('<div class="noshow_occ">+ '+(occLength-max)+'</diV>');

					var msg = '';
					if(occLength==1)
						msg = model.lang.before+occLength+model.lang.after;
					else
						msg = model.lang.before_p+occLength+model.lang.after_p;
						html[chapter].occs.push({ sequence : doc.sequence_i, content : '<section class="chapter" data-sequence="'+doc.sequence_i+'"><header><h3>'+
						doc.title_s+'<span class="chapter_occ">'+msg+'</span>'+
						'</h3></header><div class="wraper_occ">'+items.join('')+'</div></section>'});
						return occLength;
				}
			},
			/**
			* Create section
			* @method createSection
			*/
			createSection : function(doc){
				chapter = doc.id;
				html[chapter] = {occs : []};
			},
			/**
			* Close section
			* @method closeSection
			*/
			closeSection : function(){
				html[chapter].occs.sort(function(a, b) {return a.sequence - b.sequence});
			},
			/**
			* Insert title
			* @method setTitle
			* @param {object} data
			*/
			setTitle : function(data){
				var line = [];
				seqTitleTPL.forEach(function(cell){
					if(dates.indexOf(cell)>-1){
						line.push(_getDate(cell,data));
					} else if(persons.indexOf(cell)>-1){
						line.push(_getPersons(cell,data));
					} else if(cell=='title'){
						line.push(_getTitle(data));
					} else {
						line.push(_getCell(cell,data));
					}
				});
				_setTitle(line);
			},
			/**
			* Get occurences HTML
			* @method get
			* @return {string}
			*/
			get : function(){
				var _h = [];
				for(var ch in html){
					_h.push('<section>');
					_h.push(html[ch].title);
					html[ch].occs.forEach(function(h){
						_h.push(h.content);
					});
					_h.push('</section>');
				}
				return _h.join('');
			},
			/**
			* Get counters
			* @method getCounters
			* @return {string}
			*/
			getCounters : function(){
				return {
					occ : occCount,
					book : bookCount
				}
			}
		}
	};
	return OccHTML;
})();
