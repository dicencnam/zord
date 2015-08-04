window.$dialog = (function(undefined) {
	var _topZIndex = function() {
			var num = [1];
			[].forEach.call(document.querySelectorAll('*'),function(el, i){
				var x = parseInt(window.getComputedStyle(el, null).getPropertyValue("z-index")) || null;
				if(x!=null)
					num.push(x);
			});
			return Math.max.apply(null, num)+1;
	};
	var _position = function(elem) {
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
	var dialogID = '__dialog_';
	var dialogModalID = '__dialogModal_';

	var show  = function(msg,type,isModal,callback) {
		if(isModal)
			modal();
		var dialogEl = document.getElementById(dialogID);
		if(dialogEl==undefined){
			document.body.insertAdjacentHTML('beforeend','<div class="dialog" id="'+dialogID+'"></div>');
			dialogEl = document.getElementById(dialogID);
		}
		dialogEl.style.zIndex = _topZIndex()+1;
		switch( type ) {
			case 'box':
				dialogEl.innerHTML = msg;
			break;
			case 'waitMsg':
				dialogEl.innerHTML = msg;
				setTimeout(function(){
					dialog.hide();
				},1500);
			break;
		}
		if(callback!=undefined)
			callback(dialogEl);

		_position(dialogEl);

		dialogEl.style.visibility = 'visible';
	};
	var modal = function(){
		var dialogModalEl = document.getElementById(dialogModalID);
		if(dialogModalEl==undefined){
			document.body.insertAdjacentHTML('beforeend', '<div id="'+dialogModalID+'"></div>');
			dialogModalEl = document.getElementById(dialogModalID);
		}
		dialogModalEl.style.zIndex = _topZIndex();
		var body = document.body, html = document.documentElement;
		var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
		dialogModalEl.style.height = height+'px';
	};

	// -------------------------------------------------------------------------
	var dialog = {
		hide : function(){
			setTimeout(function(){
				var dialogEl = document.getElementById(dialogID);
				if(dialogEl)
					dialogEl.parentNode.removeChild( dialogEl );
				var dialogModalEl = document.getElementById(dialogModalID);
				if(dialogModalEl)
					dialogModalEl.parentNode.removeChild( dialogModalEl );
			},20);
		},

		hideDelay: function(){
			setTimeout(function(){
				dialog.hide();
			},350);
		},

		box : function(msg,callback){
			show(msg,'box',true,callback);
		},
		waitMsg : function(msg,callback){
			show(msg,'waitMsg',false,callback);
		},
		wait : function(callback){
			show('<div class="dialog-wait"></div>','box',true,callback);
		}
	};
	return dialog;
}());
