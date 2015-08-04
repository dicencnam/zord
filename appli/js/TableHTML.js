/**
* Table HTML builder

	var table = new TableHTML({
		table : true, // create <table> element true or false
		id : 'tableID',
		class : 'myClass', // optional
		tpl : [
			'creation_date_i',
			'creation_date_after_i',
			'creator_ss',
			'title',
			'editor_ss',
			'date_i',
			'book_s'
		]
	});

* @class TableHTML
* @constructor
* @module ZORD
* @submodule public
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
* @param {object} model
*/
window.TableHTML = (function(undefined) {
	var TableHTML = function(model){

		var seqTPL  = model.tpl;

		var html = [];
		if(model.table){
			var cl = '';
			if(model.class!=undefined)
				cl = ' class="'+model.class+'"';
			html.push(['<table id="'+model.id+'"'+cl+'>']);
		}

		var dates = ['creation_date_i','creation_date_after_i','date_i'];
		var persons = ['creator_ss','editor_ss'];
		var bookCount = 0;

		var _setLine = function (line,level){
			var cl = '';
			if(level>0)
				cl = ' class="draft"';
			html.push('<tr'+cl+'>'+line.join('')+'</tr>');
			bookCount++;
		};

		var _getCell = function(cell,data){
			return '<td>'+data[cell]+'</td>';
		};

		var _getDate = function(cell,data){
			var date = '';
			if(data[cell]!=undefined)
				date = data[cell];
			return '<td class="t_date">'+date+'</td>';
		};

		var _getTitle = function(data){
			var subtitle = '';
			if(data['subtitle_s']!=undefined)
				subtitle = '. '+data['subtitle_s'];
			var title = '';
			if(data['title_s']!=undefined)
				title = data['title_s'];

			var t = title+subtitle;
			return '<td class="t_title" data-sort="'+_clearTitle(t)+'"><a href="'+PATH+data['book_s']+'">'+t+'</a></td>';
		};

		var _clearTitle = function(val){
			return val.replace('>', '').replace('<', '').replace('"', '');
		}

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
			return '<td class="t_person">'+persons+'</td>';
		};

		return {
			/**
			* Insert line
			* @method set
			* @param {object} data Cells data
			*/
			set : function(data){
				var line = [];
				seqTPL.forEach(function(cell){
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
				_setLine(line,data['level_i']);
			},
			/**
			* Get table HTML
			* @method get
			* @return {string}
			*/
			get : function(){
				if(model.table)
					html.push('</table>');
				return html.join('');
			},
			/**
			* Get counter line
			* @method getCounter
			* @return {string}
			*/
			getCounter : function(){
				return bookCount;
			}
		}
	};
	return TableHTML;
})();
