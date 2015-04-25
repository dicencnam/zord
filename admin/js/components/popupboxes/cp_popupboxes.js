/**
 * @package  Component
 * @subpackage component (cp_)
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

/**
 * Composant popupboxes
 * namespace : http://www.components.org
 */
define('cp/popupboxes/cp_popupboxes',function() {
	(function() {
		var index = -1;

		var _topZIndex = function() {
			var num = [1];
			[].forEach.call(document.querySelectorAll('*'),function(el, i){
				var x = parseInt(window.getComputedStyle(el, null).getPropertyValue("z-index")) || null;
				if(x!=null)
					num.push(x);
			});
			return Math.max.apply(null, num);
		};

		var _reposition = function(elem) {
			// selon la talle de l'élément détermine le top et left
			var top = ((window.innerHeight / 2) - (elem.offsetHeight / 2)) - 50;
			var left = ((window.innerWidth / 2) - (elem.offsetWidth / 2));

			// reste dans la fenêtre
			if( top < 0 ) top = 0;
			if( left < 0 ) left = 0;

			// css sur l'élément
			elem.style.top = top + 'px';
			elem.style.left = left + 'px';
		};

		var popupboxes = {

			methods : {

				domInsert: function() {
					index++;
					this.index = index;
				},

				message : function (msg,type,modal,callback) {
					var diaID = 'dia_m'+this.index;
					var diaWarp = 'dia_warp'+this.index;


					if(modal)
						this.modal();
					var topZIndex = _topZIndex();
					var dia = document.getElementById(diaID);
					if(dia==undefined)
						document.getElementById('body').insertComponent('beforeend','<div class="dia_m" id="'+diaID+'"></div>');

					dia = document.getElementById(diaID);
					dia.style.zIndex = topZIndex+2;

					// selon le message
					switch( type ) {
						case 'wait': // attente
							dia.insertComponent('replace','<div id="'+diaWarp+'" class="dia_warp"><img style="vertical-align:middle;" src="'+this.getAttribute('wait')+'"/></div>');
						break;
						case 'waitM': // attente
							dia.insertComponent('replace','<div id="'+diaWarp+'" class="dia_warp"><img style="vertical-align:middle;" src="'+this.getAttribute('wait')+'"/>&#160;&#160;'+msg+'</div>');
						break;
						case 'box':
							dia.insertComponent('replace','<div id="'+diaWarp+'" class="dia_warp">'+msg+'</div>');
						break;
						default: // on affiche le message pendant 1500 millisecondes
							dia.insertComponent('replace','<div id="'+diaWarp+'" class="dia_warp">'+msg+'</div>');
							setTimeout(function(){
								if(dia.parentNode!=undefined)
									dia.parentNode.removeChild( dia );
							},1500);
						break;
					}

					if(callback!=undefined)
						callback();
					// positionne le message
					_reposition(dia);
					// on affiche
					dia.style.visibility = 'visible';
				},
				reposition : function () {
					var dia = document.getElementById('dia_m'+this.index);
					if(dia!=undefined){
						_reposition(dia);
					}
				},

				wait : function () {
					this.message('','wait');
				},

				waitM : function (msg) {
					this.message(msg,'waitM');
				},

				box : function (msg,modal,callback) {
					this.message(msg,'box',modal,callback);
				},

				msg : function (msg) {
					this.message(msg,'','');
				},

				requestFull : function () {
					var dia = document.getElementById('dia_m'+this.index);
					if(dia) {
						this.diaL = dia.style.left;
						this.diaT = dia.style.top;
						dia.style.left = '0';
						dia.style.top = '0';
					}
				},
				cancelFull : function () {
					var dia = document.getElementById('dia_m'+this.index);
					if(dia && this.diaT) {
						dia.style.left = this.diaL;
						dia.style.top = this.diaT;
					}
				},

				hide: function(){
					var _this = this;
					setTimeout(function(){
						var dia = document.getElementById('dia_m'+_this.index);
						if(dia)
							dia.parentNode.removeChild( dia );
					},500);
				},

				hideModal : function(){
					setTimeout(function(){
						var dia = document.getElementById('dia_m'+this.index);
						if(dia)
							dia.parentNode.removeChild( dia );
						var container = document.querySelector('.dia_container');
						if(container)
							container.parentNode.removeChild( container );
					},20);
				},

				modal : function(){
					var container = document.querySelector('.dia_container');
					// détermine le z-index
					var topZIndex = _topZIndex();
					if(container==undefined){
						var idOverlay = 'poverlay'+topZIndex;
						document.body.insertAdjacentHTML('beforeend', '<div class="dia_container" id="'+idOverlay+'"></div>');
						var container = document.getElementById(idOverlay);
					}
					container.style.zIndex = topZIndex+1;
					var body = document.body, html = document.documentElement;
					var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
					container.style.height = height+'px';
				},

				dialogue : function (type,title,msg,callback,ok_label,ok_icon){

					var _ok_label = 'Ok';
					if(ok_label!=undefined)
						_ok_label = ok_label;
					var _ok_icon = 'fa-check-square-o';
					if(ok_icon!=undefined)
						_ok_icon = ok_icon;

					var dia = document.getElementById('dia_m'+this.index);
					if(dia!=undefined)
						dia.parentNode.removeChild( dia );
					var topZIndex = _topZIndex();
					var idOverlay = 'poverlay'+topZIndex;

					document.body.insertAdjacentHTML('beforeend', '<div class="dia_container" id="'+idOverlay+'"></div>');
					var container = document.getElementById(idOverlay);
					container.style.zIndex = topZIndex+1;
					var body = document.body, html = document.documentElement;
					var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
					container.style.height = height+'px';

					var classAlert = '';
					var widthCancel = false;
					switch(type) {
						case 'alert' :
							classAlert = ' alert';
						break;
						case 'confirm' :
							widthCancel = true;
						break;
					}
					document.body.insertAdjacentHTML('beforeend', '<div class="dia_m_ts" id="dia_m'+this.index+'"><div class="dia_warp"><h1 id="dia_title'+this.index+'" class="dia_title"></h1><div id="dia_content'+this.index+'" class="dia_content'+classAlert+'"><div id="dia_message'+this.index+'" class="dia_message"></div></div></div></div>');
					var dia = document.getElementById('dia_m'+this.index);
					dia.style.zIndex = topZIndex+1;

					// insertion du titre
					document.getElementById("dia_title"+this.index).innerHTML = title;

					// insertion du message
					var messageEl = document.getElementById("dia_message"+this.index);
					messageEl.innerHTML = msg;

					// positionne le message
					_reposition(dia);

					var _buttons = '';
					if(widthCancel) {
						_buttons = '<span id="popup_cancel'+this.index+'" class="button-label" style="float:left;"><span class="fa-button-label fa-times"></span>Annuler</span><span id="popup_ok'+this.index+'" class="button-label" style="float:right;"><span class="fa-button-label '+_ok_icon+'"></span>'+_ok_label+'</span>';
					} else {
						_buttons = '<span id="popup_ok'+this.index+'" class="button-label"><span class="fa-button-label '+_ok_icon+'"></span>'+_ok_label+'</span>';
					}

					// insertion du bouton ok
					messageEl.insertAdjacentHTML('afterend', '<div id="dia_panel'+this.index+'" class="dia_panel">' + _buttons +'</div>');
					var popup_ok = document.getElementById("popup_ok"+this.index);

					var cl = function(val){
						container.parentNode.removeChild( container );
						dia.parentNode.removeChild( dia );
						if( callback )
							callback(val);
					};

					popup_ok.addEventListener("click", function(event) {
						cl(true);
					});

					if( type=='confirm') {
						var popup_cancel = document.getElementById("popup_cancel"+this.index);
						popup_cancel.addEventListener("click", function(event) {
							cl(false);
						});
						// Validation clavier (ok:enter, cancel:echap)
						popup_ok.addEventListener("keydown", function(event) {
							if( event.keyCode == 13)
								cl(true);
							if(event.keyCode == 27)
								cl(false);
						});
					} else {
						popup_ok.addEventListener("keydown", function(event) {
							if( event.keyCode == 13 || event.keyCode == 27 )
								cl(true);
						});
					}
					popup_ok.focus();

					dia.style.visibility = 'visible';
				},

				alert : function (title,msg,callback){
					this.dialogue('alert',title,msg,callback);
				},

				confirm : function (title,msg,callback,ok_label,ok_icon){
					this.dialogue('confirm',title,msg,callback,ok_label,ok_icon);
				},

				upload : function (fileObj) {
					var template = '<div id="importfiles_dia'+this.index+'" style="width:320px;height:45px;"><div id="importfiles_boxMessage"><div class="fileupload-container"><div class="fileupload-progress-bar-container"><div id="importfiles_progress'+this.index+'" class="fileupload-progress-bar"></div><div id="importfiles_percent'+this.index+'" class="fileupload-progress-percent">0%</div></div><div class="fileupload-file"><b>Name:</b> '+fileObj.name+' <b>Size:</b> '+fileObj.size+' kb</div></div></div></div>';

					this.message(template,'box');
				},

				progress : function( w ) {
					document.getElementById('importfiles_progress'+this.index).style.width = w + '%';
					document.getElementById('importfiles_percent'+this.index).innerHTML = Math.floor(w) + '%';
				}
			}
		};

		JSElem.register('http://www.components.org','popupboxes',popupboxes);
	}());
});
