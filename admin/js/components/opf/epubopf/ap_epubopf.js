/**
 * @package  Component
 * @author David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

/**
 * Composant opf - part epubopf
 * namespace : http://www.idpf.org/2007/opf
 *
 * Objectif de ce composant est de gérer le fichier OPF
 * lecture/création/distribution des différentes parties
 */
define('cp/epubopf/ap_epubopf',function() {
	(function() {

		var __epub = {
			id : null,
			targetSave : null,
			dirname : null,
			basename : null,
			guide : null,
			ncxID : null,
			ncxHref : null,
			layout : 'reflowable',
			versionInteger : 2,
			attributes : {},
			item : {},
			meta : {},
			cover : {},
			css : []
		};
		var _epub = null;

		var epubHref = function (epubDirname) {
			var path = window.location.href;
			return path.replace(/\\/g,'/').replace(/\/[^\/]*$/, '')+'/'+epubDirname;
		};

		var cssAO = function (cssList){
			return {
				getCSSList : function () {
					return cssList;
				},
				putCSSList : function (array) {
					var l = array.length;
					for (var i = 0; i < l; i++) {
						if( cssList.indexOf(array[i])==-1)
							cssList.push(array[i]);
					}
				},
				delCSSList : function (array) {
					var l = array.length;
					var n = [];
					cssList.forEach(function(v) {
						if(array.indexOf(v)==-1)
							n.push(v);
					});
					cssList = n;
				},
				updateCSSList : function (array) {
					cssList = array;
				}
			};
		};

		var _getRelativePath = function (fileRef,fileTarget) {
			var fileRefA = fileRef.split('/');
			fileRefA.pop();
			var fileTargetA = fileTarget.split('/');
			fileTargetA.forEach(function(d){
				if(d=='..') {
					fileTargetA.shift();
					fileRefA.pop();
				}
			});
			var c = '';
			if(fileRefA.length>0)
				c = '/';
			return fileRefA.join('/')+c+fileTargetA.join('/');
		};

		var _register = {};

		var cp_epubopf = {

			template : '<div anonid="in"></div><div anonid="out"></div>',

			methods : {

				domString : function () {
					return '<cp:epubopf' + this.attrToString()+ '></cp:epubopf>';
				},

				load : function (id, opf, targetSave, dirname, basename) {
					_epub = JSON.parse( JSON.stringify( __epub ) );

					var _in = this.getAnonid('in');
					_in.innerHTML = opf;

					var pack = _in.querySelector('opf\\:package');

					_epub.get = function(element) {
						return pack.querySelector('opf\\:'+element);
					};

					_register = {};
					_epub.id = id;
					_epub.uniqueIdentifier = pack.getAttribute('unique-identifier');
					_epub.targetSave = targetSave;
					_epub.dirname = dirname;
					_epub.basename = basename;
					_epub.ncxID = null;
					_epub.ncxHref = null;
					_epub.attributes = {};
					_epub.guide = {};
					_epub.item = {};
					_epub.meta = {};
					_epub.cover = {};
					_epub.nav = {};
					_epub.css = [];

					[].forEach.call(pack.attributes, function(att) {
						_epub.attributes[att.nodeName] = att.value;
					});

					_epub.versionInteger = parseInt(_epub.attributes.version,10);

					[].forEach.call(pack.querySelectorAll('opf\\:reference'), function (element) {
						var href = element.getAttribute('href');
						var title = element.getAttribute('title');
						var type = element.getAttribute('type').toLowerCase();
						_epub.guide[href] = {
							type : type,
							title : title,
							href : href
						};
					});

					// meta
					[].forEach.call(pack.querySelectorAll('opf\\:meta'), function (element) {
						if (element.attributes.length > 0) {
							var attributes = {};
							[].forEach.call(element.attributes, function(att) {
								attributes[att.nodeName] = att.value;
							});

							var _name = element.getAttribute('name');
							var _property = element.getAttribute('property');
							if(_name)
								_epub.meta[_name] = attributes;
							else if(_property && _property!="role")
								_epub.meta[_property] = {attr:attributes,content:element.innerHTML};
							else if(_property){
								var _refines = element.getAttribute('refines');
								if(_refines)
									_epub.meta[_refines] = {attr:attributes,content:element.innerHTML};
							}
						}
						element.parentNode.removeChild( element );
					});
					if(_epub.meta['rendition:layout'])
						_epub.layout = _epub.meta['rendition:layout'].content;

					// item
					var _css = [];
					[].forEach.call(pack.querySelectorAll('opf\\:item'), function (element) {
						if (element.attributes.length > 0) {
							var attributes = {};
							[].forEach.call(element.attributes, function(att) {
								attributes[att.nodeName] = att.value;
							});
							_epub.item[element.getAttribute('href')] = attributes;

							if(_epub.meta.cover!=undefined && element.getAttribute('id')==_epub.meta.cover.content)
								_epub.cover = attributes;

							if(attributes.properties && attributes.properties=='cover-image')
								_epub.cover = attributes;

							if(attributes.properties && attributes.properties=='nav')
								_epub.nav = attributes;


							if(element.getAttribute('media-type')=='text/css')
								_css.push(element.getAttribute('href'));
						}
					});

					_epub.css = cssAO(_css);

					// NCX
					var spineToc = pack.querySelector('opf\\:spine').getAttribute('toc');

					if(spineToc!=undefined) {
						var item = pack.querySelector('opf\\:item[id="'+spineToc+'"]');

						_epub.ncxID = spineToc;
						if(item!=null)
							_epub.ncxHref = item.getAttribute('href');
					}

					[].forEach.call(pack.querySelectorAll('opf\\:itemref'), function (element) {
						var idref = element.getAttribute('idref');
						var item = pack.querySelector('opf\\:item[id="'+idref+'"]');
						var mediaType = item.getAttribute('media-type');
						var href = item.getAttribute('href');

						element.setAttribute('href',href);
						element.setAttribute('media-type',mediaType);

						if(_epub.guide[href]!=undefined)
							element.setAttribute('guidetype',_epub.guide[href].type);
					});

					var dcEl = {
						title : true,
						creator : true,
						subject : true,
						description : true,
						type : true,
						format : true,
						language : true,
						rights : true,
						date : true,
						identifier : true
					}

					var metadata = pack.querySelector('opf\\:metadata');
					[].forEach.call(metadata.querySelectorAll('*'), function (element) {
						var nodeName = element.nodeName.toLowerCase().split(':')
						dcEl[nodeName[1]] = false;
					});

					var oblDCelement = metadata.innerHTML;
					for(var d in dcEl) {
						if(dcEl[d])
							oblDCelement += '<dc:'+d+'></dc:'+d+'>\n';
					}
					metadata.innerHTML = oblDCelement;
				},

				save : function(msg, callback) {
					var version = _epub.versionInteger;
					var dependenciessearch = this.getAttribute('dependenciessearch');

					var opf = $notify.pub(_epub.targetSave,[{
						metadata : '',
						meta : '\t\t<meta content="'+HIBOUK_VERSION+'" name="Hibouk version"/>\n',
						manifest : {},
						spine : {string:'',files:{}},
						guide : '',
						msg : msg
					}]);
					opf = opf[0];

					// création d'une iframe
					// pour rechercher dans les fichiers les dépendances
					var ifFind = document.createElement('iframe');
					ifFind.style.display = 'none';
					document.body.appendChild(ifFind);
					var countFilesSpine = 0;
					var lengthFilesSpine = opf.spine.files.length;
					var manifestDep = {};
					var linkOpf = epubHref(_epub.dirname);
					var cssFiles = [];

					// fichiers dans les css
					var cssImg = function () {
						var cssList = _epub.css.getCSSList();
						if(cssList.length>0){
							var re = /url\((['"]?)(.+?)\1\)/gi;

							cssList.forEach(function(item,i){
								if(manifestDep[linkOpf+item]!=undefined)
									cssFiles.push(item);
							});
							if(cssFiles.length>0){
								var cssListLength = cssFiles.length-1;
								cssFiles.forEach(function(item,i){
									var _hrefCSS = linkOpf+item;
									$req('xhr!'+_hrefCSS, function (f) {
										while (result = re.exec(f)){
											var _result = _getRelativePath(item,result[2]);
											manifestDep[linkOpf+_result] = 'no';
										}
										if(i==cssListLength)
											finalAction();
									});
								});
							} else {
								finalAction();
							}
						} else {
							finalAction();
						}
					};

					var finalAction = function () {
						// finaliser le manifest
						var sizeURL = linkOpf.length;

						var c_manifestDep = 0;
						var typeMime = {
							'css' : 'text/css',
							'js' : 'application/javascript',
							'jpe' : 'image/jpeg',
							'jpeg' : 'image/jpeg',
							'jpg' : 'image/jpeg',
							'png' : 'image/png',
							'svg' : 'image/svg+xml'
						};

						for (var f in manifestDep) {
							c_manifestDep++;
							var file = f.substring(sizeURL);

							if(opf.manifest[file]==undefined) {
								var ext = file.substring(file.lastIndexOf('.')+1).toLowerCase();
								var tm = typeMime[ext];
								var id = 'dep'+c_manifestDep;

								opf.manifest[file] = {
									id : id,
									href : file,
									'media-type' : tm
								};
							}
						}

						// le manifest en texte
						var _manifest = '';
						var _manifestFiles = [];
						for (var i in opf.manifest) {
							_manifest += '\t\t<item';
							for(var a in opf.manifest[i]) {
								_manifest += ' '+a+'="'+opf.manifest[i][a]+'"';

								if(a=='href')
									_manifestFiles.push(opf.manifest[i][a]);
							}
							_manifest += '></item>\n';
						}
						opf.manifest = {
							string : _manifest,
							files : opf.manifest,
							types : opf.types
						};

						var opfFinalTXT = '<?xml version="1.0" encoding="UTF-8" ?>\n';
						opfFinalTXT += '<package xmlns="http://www.idpf.org/2007/opf" version="2.0" unique-identifier="'+_epub.uniqueIdentifier+'">\n';
						opfFinalTXT += '\t<metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf">\n';
						opfFinalTXT += opf.metadata;
						opfFinalTXT += opf.meta;
						opfFinalTXT += '\t</metadata>\n';
						opfFinalTXT += '\t<manifest>\n';
						opfFinalTXT += opf.manifest.string;
						opfFinalTXT += '\t</manifest>\n';
						opfFinalTXT += opf.spine.string;
						opfFinalTXT += '\t<guide>\n';
						opfFinalTXT += opf.guide;
						opfFinalTXT += '\t</guide>\n';
						opfFinalTXT += '</package>';

						opfFinalTXT = opfFinalTXT.replace(/ +\t\t</g,'\t\t<')
							.replace(/ +\n/g,'\n')
							.replace(/\n{2,}/g,'\n');

						// suppression de l'iframe
						// setTimeout => exception firefox
						setTimeout(function(){
							document.body.removeChild( ifFind );
						}, 100);

						var savefiles = [{
							file : _epub.basename,
							content : opfFinalTXT
						}];

						if(opf.ncx!=undefined)
							savefiles.push({file:opf.ncx.href,content:opf.ncx.content});

						callback(_manifestFiles,savefiles,cssFiles);
					};

					ifFind.onload = function (){
						var sizeURL = epubHref(_epub.dirname).length;
						var src = ifFind.src;
						if(src!='') {
							src = src.substring(sizeURL);

							if(version<3) {
								// guide
								var type = opf.spine.types[src];

								if(type!='no')
									opf.guide += '\t\t<reference type="'+type+'" title="'+type+'" href="'+src+'"/>\n';
							}

							countFilesSpine++;
							// rechercher dans le fichier des dépendances
							// link => href;
							// script => src
							// img => src
							msg(dependenciessearch+countFilesSpine+'/'+lengthFilesSpine);
							[].forEach.call(ifFind.contentWindow.document.querySelectorAll('link, script, img'), function (element) {
								var l = 'src';
								if(element.nodeName.toLowerCase()=='link')
									l = 'href';

								var file = element[l];

								if(file!=undefined)
									manifestDep[file] = type;
							});

							if(lengthFilesSpine==countFilesSpine)
								cssImg();
							else
								ifFind.src = _epub.dirname + opf.spine.files[countFilesSpine];
						}
					};

					// on charge le premier item
					if(lengthFilesSpine>0) {
						ifFind.src = _epub.dirname + opf.spine.files[0];
					} else { // conservation des fichiers css
						cssFiles = _epub.css.getCSSList();
						var cssListLength = cssFiles.length-1;
						if(cssListLength>-1){
							cssFiles.forEach(function(item,i){
								manifestDep[linkOpf+item] = 'no';
								if(i==cssListLength)
									finalAction();
							});
						} else {
							finalAction();
						}
					}
				},

				register : function (targetName,element) {
					_register[targetName] = element;
				},

				getRegister : function (targetName) {
					if(_register[targetName]!=undefined)
						return _register[targetName];
					else
						return null;
				},

				getTargetSave : function() {
					return _epub.targetSave;
				},

				getDirname : function() {
					return _epub.dirname;
				},

				getEpubVersion : function() {
					return _epub.versionInteger;
				},

				getSpine : function() {
					return _epub.get('spine').outerHTML;
				},

				getMetadata : function() {
					return _epub.get('metadata').outerHTML;
				},
				getNcx : function() {
					return {
						id : _epub.ncxID,
						href : _epub.ncxHref,
						dirname : _epub.dirname
					};
				},
				getNav : function() {
					return _epub.nav;
				},
				getCover : function() {
					return _epub.cover;
				},

				setCover : function( cover ) {
					_epub.cover = cover;
				},

				getGuide : function( name ) {
					if(_epub.guide[name]!=undefined)
						return _epub.guide[name];
					else
						return null;
				},

				setGuide : function( href, title, type ) {
					_epub.guide[href] = {
						type : type,
						title : title,
						href : href
					};
				},
				isFixedLayout : function(){
					if(_epub.layout=='pre-paginated')
						return true;
					else
						return false;
				},
				getCSSList : function () {
					return _epub.css.getCSSList();
				},
				putCSSList : function (array) {
					_epub.css.putCSSList(array);
				},
				delCSSList : function (array) {
					_epub.css.delCSSList(array);
				},
				updateCSSList : function (array) {
					_epub.css.updateCSSList(array);
				},
				relativeURL : function (url)  {
					var lg = url.split('/').length-1;
					var v = '';
					for(var i = 0; i < lg; i++)
							v += '../';
					return v;
				}
			}
		};

		JSElem.register('http://www.components.org','epubopf',cp_epubopf);

	}());
});