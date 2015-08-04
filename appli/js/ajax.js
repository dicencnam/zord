
/**
* @name $ajax
* @namespace
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
(function(undefined) {

	var queryString = function ( mode, data ) {
		var result = [];
		for (var key in data)
			result.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));

		var txt = result.join( "&" ).replace( /%20/g, "+" );

		if(mode=='GET') {
			if(txt=='')
				return null;
			else
				return '?' + txt;
		} else {
			return txt;
		}
	};

	var _Msg = null;

	var ajax = {

		/**
		* Set message component for upload
		* @name setMsgComponent
		* @memberof $ajax
		* @param {number} [msg] How many times to repeat the string.
		*/
		setMsgComponent : function ( msg ) {
			_Msg = msg;
		},

		/**
		* ajax
		* @name ajax
		* @memberof $ajax
		*/
		ajax : function ( properties ) {

			var stateChange = function () {
				if (this.readyState===4) {
					if(this.status===200) {
						if(properties.success!==undefined) {
							if(this.getResponseHeader("Content-Type")=='text/xml')
								properties.success(this.responseXML);
							else
								properties.success(this.responseText);
						}
					} else {
						if(properties.error!==undefined)
							properties.error(this.status,this.responseText);
					}
				}
			};

			var request = new XMLHttpRequest();

			if(properties.responseType!==undefined) // "arraybuffer", "blob", "document", "json", and "text"
				request.responseType = properties.responseType;

			if (request) {
				request.onreadystatechange = stateChange;
				if (properties.methode==='GET') {
					request.open("GET", queryString('GET', properties.url), true);
					request.send(null);
				} else {
					request.open("POST", properties.url , true);
					request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
					request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
					// request.setRequestHeader('Connection', 'close');
					request.send(queryString('POST',properties.data));
				}
			}
		},

		/**
		* @name getXML
		* @memberof $ajax
		*/
		getXML : function ( properties ) {
			if(properties.data===undefined)
				properties.data = {};

			if(properties.error!==undefined){
				var _error = properties.error;
				properties.error = function ( error, data ) {
					_error.call(this,error,data);
				};
			}

			if(properties.success!==undefined){
				var _success = properties.success;
				properties.success = function ( data ) {
					_success.call(this, data);
				};
			}

			ajax.ajax({
				methode : 'POST',
				url : properties.url,
				data : properties.data,
				success : properties.success,
				error : properties.error
			});
		},

		/**
		* @name getJSON
		* @memberof $ajax
		*/
		getJSON : function ( properties ) {
			if(properties.data===undefined)
				properties.data = {};

			if(properties.error!==undefined){
				var _error = properties.error;
				properties.error = function ( error, data ) {
					var json = JSON.parse(data);
					_error.call(this,error,json);
				};
			}

			if(properties.success!==undefined){
				var _success = properties.success;
				properties.success = function ( data ) {
					var json = JSON.parse(data);
					_success.call(this,json);
				};
			}

			ajax.ajax({
				methode : 'POST',
				url : properties.url,
				data : properties.data,
				success : properties.success,
				error : properties.error
			});
		},

		/**
		* @name upload
		* @memberof $ajax
		*/
		upload : function ( properties ) {

				// url : properties.url,
				// data : properties.data,
				// files : properties.files,
				// name : properties.name,
				// success : properties.success,
				// next : properties.success,
				// error : properties.error

			var _success = undefined;
			var _error = undefined;

			if(properties.name===undefined)
				properties.name = 'fileToUpload';
			if (typeof properties.files !== "undefined") {
				for (var i=0, l=properties.files.length; i<l; i++){
					if(i==properties.files.length-1){
						if(properties.success!==undefined )
							properties._success = properties.success;
						if(properties.error!==undefined)
							properties._error = properties.error;
					}
					ajax._upload(properties.files[i],properties);
				}
			} else {
				_Msg.msg("No support for the File API in this web browser");
			}
		},

		_upload : function ( file, properties ) {

			_Msg.upload({
				name : file.name,
				size : parseInt(file.size / 1024, 10)
			});

			xhr = new XMLHttpRequest();

			xhr.upload.addEventListener("progress", function (evt) {
				if (evt.lengthComputable) {
					var w = (evt.loaded / evt.total) * 100;
					_Msg.progress(w);
					if(w===100 && properties.next!==undefined)
						properties.next();
				}
			}, false);

			// File uploaded
			xhr.addEventListener("load", function () {
				_Msg.progress(100);
				if(this.status==200 && properties._success!==undefined){
					var json = JSON.parse(this.responseText);
					properties._success(json);
				} else if(properties._error!==undefined){
					var json = JSON.parse(this.responseText);
					properties._error(this.status,json.message);
				}
			}, false);

			var url = properties.url+queryString('GET',properties.data);
			xhr.open("post", url, true);

			var fd = new FormData();
			fd.append(properties.name, file);

			// Send the file
			xhr.send(fd);
		},

		/**
		* Get values from form
		* @name getFormValues
		* @memberof $ajax
		*/
		getFormValues : function( form ) {
			var response = {
				empty : false,
				emptyName : [],
				values : {}
			};
			var getName = function(name,id){
				if(name!=undefined)
					return name;
				else if(id!==undefined)
					return id;
				return '';
			};
			[].forEach.call(document.getElementById(form).querySelectorAll('input,select,textarea'),	function(el){
				var nodeName = el.nodeName.toLowerCase();
				el.style.outline = "1px solid transparent";
				var name = getName(el.getAttribute('name'),el.getAttribute('id'));
				var empty = el.getAttribute('data-empty');
				var value = el.value;
				switch(nodeName) {
					case 'input' :
						var type = el.getAttribute('type');
						if(type!=="button" && type!=='submit' && type!=='radio' && type!=='checkbox' && type!=='file'){
							if(value==='' && empty!==undefined && empty==='no'){
								response.empty = true;
								response.emptyName.push(name);
								el.style.outline = "1px solid red";
							}
							response.values[name] = el.value;
						} else if(type==="file") {
							var hasValue = false;
							if(empty!==undefined && empty==='no'){
								if(el.files.length!=0){
									var extentions = el.getAttribute('data-extension');
									var extArray = extentions.split(',');
									var file = el.files[0].name;
									var ext = file.substring(file.lastIndexOf(".")+1, file.length).toLowerCase();
									if(extArray.indexOf(ext) > -1){
										hasValue = true;
									} else {
										MSG.msg('Incorrect type of file ('+extentions+')');
									}
								}
							}
							if(hasValue){
								response.values[name] = el.files;
							} else {
								response.empty = true;
								response.emptyName.push(name);
								el.style.outline = "1px solid red";
							}
						}
					break;
					case 'select' :
						response.values[name] = el.value;
					break;
					case 'textarea' :
						if(value==='' && empty!==undefined && empty==='no'){
							response.empty = true;
							response.emptyName.push(name);
							el.style.outline = "1px solid red";
						}
						response.values[name] = el.value;
					break;
				}
			});

			[].forEach.call(document.getElementById(form).querySelectorAll('input:checked'),function(el){
				var name = getName(el.getAttribute('name'),el.getAttribute('id'));
				response.values[name] = el.value;
			});
			return response;
		}
	};
	window.$ajax = ajax;
})();
