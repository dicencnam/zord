/**
 * @package Liseuse
 * @author David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */


/**
 * Composant epubliseuse
 * namespace : http://www.components.org
 */
define('cp/epubliseuse/lis_epubliseuse', function() {
	(function() {

		var _waitStart = true;

		/*! viewportSize | Author: Tyson Matanich, 2013 | License: MIT */
		var _getSize = function (Name) {
				var size;
				var name = Name.toLowerCase();
				var document = window.document;
				var documentElement = document.documentElement;
				if (window["inner" + Name] === undefined) {
						// IE6 & IE7 don't have window.innerWidth or innerHeight
						size = documentElement["client" + Name];
				}
				else if (window["inner" + Name] != documentElement["client" + Name]) {
						// WebKit doesn't include scrollbars while calculating viewport size so we have to get fancy

						// Insert markup to test if a media query will match document.doumentElement["client" + Name]
						var bodyElement = document.createElement("body");
						bodyElement.id = "vpw-test-b";
						bodyElement.style.cssText = "overflow:scroll";
						var divElement = document.createElement("div");
						divElement.id = "vpw-test-d";
						divElement.style.cssText = "position:absolute;top:-1000px";
						// Getting specific on the CSS selector so it won't get overridden easily
						divElement.innerHTML = "<style>@media(" + name + ":" + documentElement["client" + Name] + "px){body#vpw-test-b div#vpw-test-d{" + name + ":7px!important}}</style>";
						bodyElement.appendChild(divElement);
						documentElement.insertBefore(bodyElement, document.head);

						if (divElement["offset" + Name] == 7) {
								// Media query matches document.documentElement["client" + Name]
								size = documentElement["client" + Name];
						}
						else {
								// Media query didn't match, use window["inner" + Name]
								size = window["inner" + Name];
						}
						// Cleanup
						documentElement.removeChild(bodyElement);
				}
				else {
						// Default to use window["inner" + Name]
						size = window["inner" + Name];
				}
				return size;
		};

		var merge = function(obj1, obj2) {
			var x = Object.create(obj1);
			var y = Object.create(obj2);
			for (var name in y)
			if (typeof y[name] === 'object' && x[name] !== undefined)
				x[name] = merge(x[name], y[name]);
			else
				x[name] = y[name];
			return x;
		};

		var updatePageFontSize = function(_this,width){
			_this.pageFontSize = parseInt(width/37);
		};

		var getprop = function(proparray) {
			var root = document.documentElement;
			for (var i = 0; i < proparray.length; i++)
			if (typeof root.style[proparray[i]] == "string") return proparray[i];
		};

		var sliderElement = function (_this,sliderCP){
			var width = 0;
			var max = 0;
			var visible = true;
			return {
				init : function(v){
					visible = v;
					if(!visible)
						sliderCP.style.display = 'none';
				},
				show : function(){
					if(visible){
						if((max*10)>width || max==0)
							sliderCP.style.display = 'none';
						else
							sliderCP.style.display = 'block';
					}
				},
				width : function(w){
					if(visible){
						width = w;
						sliderCP.style.width = w +'px';
						this.show();
					}
				},
				creat : function(t,m){
					if(visible){
						max = m;
						var liHtml = '';
						for (var i = 1; i <= max; i++) {
							liHtml += '<li p="'+i+'"></li>';
						};
						sliderCP.innerHTML = liHtml;
						var clickEvent = function(e) {
							var p = e.target.getAttribute('p');
							if(p!=undefined)
								t.change(p*1);
						};
						sliderCP.removeEventListener('click', clickEvent, false);
						sliderCP.addEventListener('click', clickEvent, false);
						this.show();
					}
				},
				change : function(oldP,newP){
					if(visible){
						var puce = sliderCP.querySelector("li[p='" +oldP+ "']");
						if(puce!=undefined)
							puce.className = "";
						puce = sliderCP.querySelector("li[p='" +newP+ "']");

						puce.className = "on";
					}
				}
			};
		};

		var sliderPages = function (_this){
			var position = 1;
			var max = 1;
			var visible = true;
			var width = 0;
			return {
				setValue : function(p){
					position = p;
				},
				getValue : function(){
					return position;
				},
				setMax : function(m){
					if(max!=m){
						max = m;
						if(visible)
							_this.slider_element.creat(this,max);
					}
				},
				getMax : function(){
					return max;
				},
				hide : function(){
					visible = false;
				},
				change : function(p){
					if(visible)
						_this.slider_element.change(position,p);
					position = p;

					_this.slide((position * -_this.pageWidth) + _this.pageWidth);
					_this.notify.pub('liseuse:showPosition',[{position:position,max:max}]);
				},
			};
		};
		var sliderFiles = function (_this){
			var multifiles = false;
			var max = 0;
			return {
				position : 1,
				change : function(p){
					if(multifiles){
						_this.notify.pub('liseuse:sliderFiles/change',[{oldP:this.position,newP:p}]);
						if(!_this.fixed)
							this.position = p;
					}
				},
				init : function(files){
					multifiles = files;
					if(_this.fixed)// doubles pages
						max = parseInt(files.length/2)+1;
					else
						max = files.length;
					_this.slider_element.creat(this,max);
				},
				reload : function(){
					this.change(this.position);
				},
				getValue : function(){
					return this.position;
				},
				getMax : function(){
					return max;
				}
			}
		};

		var getProperty = function (){

			var getprop = function(proparray) {
					var root = document.documentElement;
					for (var i = 0; i < proparray.length; i++)
					if (typeof root.style[proparray[i]] == "string") return proparray[i]
			};

			var transform = ['transform', 'OTransform', 'msTransform', 'MozTransform', 'WebkitTransform'];
			var transition = ['transition', 'OTransition', 'msTransition', 'MozTransition', 'WebkitTransition'];
			var transitionend = {
				'transition': 'transitionend',
				'OTransition':'oTransitionEnd',
				'msTransition': 'MSTransitionEnd',
				'MozTransition':'transitionend',
				'WebkitTransition':'webkitTransitionEnd'
			}
			var tr = getprop(transition);
			return {
				transform : getprop(transform),
				transition : tr,
				transitionend : transition[tr]
			}
		};

		var afterTransition = null;

		var epubliseuse = {
			template : function () {/*
<div class="liseuse_nav" anonid="liseuse_nav"></div>
<div class="liseuse_liseuse" anonid="liseuse">
<div class="liseuse_buttons liseuse_bl" anonid="bl"></div>
<div class="liseuse_v" anonid="v">
<span style="display:inline-block;float:left;margin-left:-15px;width:47px;">&#160;</span>
<span anonid="vtxt"></span></div>
<div class="liseuse_buttons liseuse_br" anonid="br"></div>

<div anonid="screen">
	<div anonid="liseuse_reflowable">
	<iframe anonid="liseuse_iframe" class="liseuse_iframe" scrolling="no"/>
	</div>
</div>

<div class="liseuse_tools" anonid="tools">
	<div anonid="buttons">
		<div class="liseuse_buttons liseuse_trn" anonid="liseuse_switch"></div>
		<div class="liseuse_buttons liseuse_bnav" anonid="liseuse_Nav" style="display:none;"></div>
		<div class="liseuse_buttons liseuse_cp" anonid="liseuse_Cp"></div>
		<div class="liseuse_buttons liseuse_cm" anonid="liseuse_Cm"></div>
	</div>
	<ul anonid="liseuse_slider" class="liseuse_slider"></ul>
</div>
</div>
*/},
			methods : {
				/**
				*/
				domString : function () {
					return '<cp:epubliseuse' + this.attrToString('id')+ '></cp:epubliseuse>';
				},
				/*
this.defaultOptions = {
	counter : true,
	externalLink : true,
	fixed : false,
	slider : true,
	logo : '<a class="archicol" '
};
				*/
				init : function (newOptions) {
					var _this = this;
					// update options
					if(newOptions!=undefined)
						this.options = merge(this.defaultOptions,newOptions);
					else
						this.options = this.defaultOptions;
					// logo
					this.getAnonid('v').insertAdjacentHTML('beforeend', this.options.logo);
					// counter
					if(!this.options.counter)
						this.getAnonid('vtxt').style.visibility = 'hidden';
					// slider
					this.slider_element.init(this.options.slider);
					// notification
					this.notify.pub('liseuse:init');

					var _liseuseToLeft = function(){
						if(!_this.waitTurnPage){
							var v = _this.slider_pages.getValue()-1;

							if(_this.landscape && !_this.even(v))
								v = v-1;

							if(v>=1) {
								_this.slider_pages.change(v);
							} else if(_this._multifiles!=undefined) {
								var vFiles = _this.slider_files.getValue()-1;
								if(vFiles>=1) {
									_this._rightDirection = false;
									_this.slider_files.change(vFiles);
								}
							}
						} else {
							setTimeout(function(){
								_liseuseToLeft();
							},10);
						}
					};

					var _liseuseToRight = function(){
						if(!_this.waitTurnPage){
							var max = _this.slider_pages.getMax();
							var v = _this.slider_pages.getValue()+1;
							if(_this.landscape && !_this.even(v))
								v = v+1;

							if(v<=max) {
								_this.slider_pages.change(v);
							} else if(_this._multifiles!=undefined) {
								var maxFiles = _this._multifiles.length;
								var vFiles = _this.slider_files.getValue()+1;
								if(vFiles<=maxFiles) {
									_this._rightDirection = true;
									_this.slider_files.change(vFiles);
								}
							}
						} else {
							setTimeout(function(){
								_liseuseToRight();
							},10);
						}
					};
					this.notify.sub('liseuse:toLeft',function(){
						if(!_this.fixed)
							_liseuseToLeft();
					});

					this.notify.sub('liseuse:toRight',function(){
						if(!_this.fixed)
							_liseuseToRight();
					});

					this.notify.sub('liseuse:link/anchor',function(data){
						if(!_this.fixed)
							_this.slider_pages.change(_this.getPageElement(data.anchor));
						return [data];
					});

					this.notify.sub('liseuse:link/file',function(data){
						if(!_this.fixed)
							_this.slider_files.change(data.file+1);
						return [data];
					});

					this.notify.sub('liseuse:sliderFiles/change',function(data){
						if(!_this.fixed){
							_this.slider_element.change(data.oldP,data.newP);
							_this.setAttribute('src',_this._multifiles[data.newP-1]);
						}
						return [data];
					});

					this.notify.sub('liseuse:showPosition',function(data){
						if(!_this.fixed && _this.options.counter) {
							if(_this._multifiles)
								var html = data.position+'/'+data.max+' − '+_this.slider_files.getValue()+'/'+_this.slider_files.getMax();
							else
								var html = data.position+'/'+data.max;
							_this.getAnonid('vtxt').innerHTML = html;
						}
						return [data];
					});

					this.notify.sub('liseuse:loadEpub',function(){
						if(!_this.fixed) {
							_this.getAnonid('liseuse_reflowable').style.display = 'block';
							_this.getAnonid('liseuse_Cm').style.display = 'block';
							_this.getAnonid('liseuse_Cp').style.display = 'block';
							_this.slider_files.change(1);
						}
					});
				},

				domInsert : function () {
					var _this = this;
					this.pageFontSize = 11;
					this.marge = 10;
					this.prop = {};
					this.nav = false;
					this.waitTurnPage = false;
					this.mobile = false;
					this.fixed = false;
					this.fullScreen = false;
					this.defaultOptions = {
						counter : true,
						externalLink : true,
						fixed : false,
						slider : true,
						logo : '<a class="archicol" href="http://www.archicol.fr/" target="_blank">@<span class="archi_t">rch<span class="archi_i">i</span>col</span></a>'
					};

					var switchB = this.getAnonid('liseuse_switch');
					var iframe = this.getAnonid('liseuse_iframe');
					var nav = this.getAnonid('liseuse_nav');

					this.slider_pages = sliderPages(this);
					this.slider_files = sliderFiles(this);
					this.slider_element = sliderElement(this,this.getAnonid('liseuse_slider'));
					this.prop = getProperty();
					this.mobile = (function() {
						var apple_phone = /iPhone/i,
						apple_ipod = /iPod/i,
						apple_tablet = /iPad/i,
						android_phone = /(?=.*\bAndroid\b)(?=.*\bMobile\b)/i, // Match 'Android' AND 'Mobile'
						android_tablet = /Android/i,
						windows_phone = /IEMobile/i,
						windows_tablet = /(?=.*\bWindows\b)(?=.*\bTouch\b)/i, // Match 'Windows' AND 'Touch'
						other_blackberry = /BlackBerry/i,
						other_opera = /Opera Mini/i,
						other_firefox = /(?=.*\bFirefox\b)(?=.*\bMobile\b)/i; // Match 'Firefox' AND 'Mobile'

						var ua = navigator.userAgent;
						var match = function(regex, userAgent) {
							return regex.test(ua);
						};
						return match(apple_phone) || match(apple_ipod) || match(apple_tablet) || match(android_phone) || match(android_tablet) || match(windows_phone) || match(windows_tablet) || match(other_blackberry) || match(other_opera) || match(other_firefox);
					})();

					this.notify.pub('liseuse:domInsert');

					if(this.mobile) {
						// switch (rotation) automatique en mobile
						switchB.style.display = 'none';
						[].forEach.call(document.querySelectorAll('.liseusehide'), function (element) {
							element.style.display = 'none'
						});

						var switchOrientation = function(){
							switch(window.orientation) {
								case -90:
								case 90:
									_this.setAttribute('landscape','true');
								break;
								default:
									_this.setAttribute('landscape','false');
								break;
							}

							setTimeout(function(){
								var __w = _this.viewportSize.getWidth();
								var __h = _this.viewportSize.getHeight();
								var _o = __w;
								switch(window.orientation) {
									case -90:
									case 90:
										_o = __w/2;
									break;
								}
								// font-size
								updatePageFontSize(_this,_o);

								_this.setAttribute('width',__w);
								_this.setAttribute('height',__h);
								document.body.style.width = __w + 'px';
								document.body.style.height = __h + 'px';
							}, 200);
						};
						window.addEventListener('orientationchange', switchOrientation, true);
						switchOrientation();
					} else {
						// desktop !
						var w = _this.viewportSize.getWidth();
						var h = _this.viewportSize.getHeight();
						var maxHeigth = 1400;
						if(h>maxHeigth){
							var _h = h;
							h = maxHeigth;
							w = parseInt((maxHeigth*w)/_h);
						}

						var _h = h;
						w = h;
						// rapport hauteur/largeur type ipad
						h = parseInt(_h/1.33);

						updatePageFontSize(_this,w);

						_this.startW = w;
						_this.startH = h;
						_this.setAttribute('width',w);
						_this.setAttribute('height',h);

						switchB.addEventListener('click', function (ev) {
							var l = _this.getAttribute('landscape');
							if(l=="false")
								_this.setAttribute('landscape','true');
							else
								_this.setAttribute('landscape','false');
						});

							setTimeout(function(){
								var iframe = _this.getAnonid('liseuse_iframe');
								if(iframe.contentWindow.document)
									_this.setAttribute('landscape','false');

								var _event = document.createEvent("HTMLEvents");
								_event.initEvent('click', true, true);
								_this.getAnonid('liseuse_Nav').dispatchEvent(_event);
							},800);

					}

					this._rightDirection = true;
					// ----------------------------------------------------------
					// reflowable
					iframe.addEventListener('load', function() {
						if(iframe.getAttribute('src')!=null && !_this.fixed){

							var doc = iframe.contentWindow.document;
							doc.body.parentNode.setAttribute('lang',_this.getAttribute('lang'));
							doc.body.insertAdjacentHTML('afterbegin', '<div id="__starttag__"></div>');
							doc.body.insertAdjacentHTML('beforeend', '<div id="__endtag__" style="margin:0;padding:0;font-size:1px;">&#160;</div>');
							var background = iframe.contentWindow.getComputedStyle(doc.body,null).getPropertyValue("background-color");
							var _load = true;
							_this.slider_pages.setValue(1);

							if(background!='transparent' && background!='rgba(0, 0, 0, 0)')
								_this.getAnonid('screen').style.backgroundColor = background;

							[].forEach.call(doc.querySelectorAll('body > *'), function (element) {
								var styleC = iframe.contentWindow.getComputedStyle(element,null);
								var padL = parseInt(styleC.getPropertyValue("margin-left"));
								var padR = parseInt(styleC.getPropertyValue("margin-right"));
								if(padL<10)
									element.style.marginLeft = _this.marge+'px';
								if(padR<10)
									element.style.marginRight = _this.marge+'px';
							});
							_this.initLinks(doc);

							afterTransition = function(){
								var x = _this.getPageElement('__endtag__');
								_load = false;
								_this.slider_pages.setMax(x);
								if(_this._rightDirection==false){
									_this._rightDirection = true;

									if(_this.landscape)
										_this.slider_pages.change(x-1);
									else
										_this.slider_pages.change(x);

								} else if(_this._gotoAnchor!=undefined) {
									var pElem = _this.getPageElement(_this._gotoAnchor);
									_this._gotoAnchor = undefined;
									_this.slider_pages.change(pElem);
								} else {
									var v = _this.slider_pages.getValue();
									if(v>x) {
										if(x>1 && !_this.even(x) && _this.landscape)
											v = x-1;
									}
									_this.slider_pages.change(v);
								}
							};

							doc.addEventListener(_this.prop.transitionend, function() {
								afterTransition();
							}, false);

							// bug chrome
							setTimeout(function(){
								if(_load)
									afterTransition();
							}, 500);

							_this.fileUdapte(_this);
						}
					});

					// ----------------------------------------------------------
					// buttons
					this.getAnonid('bl').addEventListener('click', function() {
						_this.notify.pub('liseuse:toLeft');
					});

					this.getAnonid('br').addEventListener('click', function() {
						_this.notify.pub('liseuse:toRight');
					});

					this.getAnonid('liseuse_Cp').addEventListener('click', function() {
						if (_this.pageFontSize < 40) {
							_this.pageFontSize++;
							iframe.contentWindow.document.body.style.fontSize = _this.pageFontSize + 'px';
						}
					});

					this.getAnonid('liseuse_Cm').addEventListener('click', function() {
						if (_this.pageFontSize > 5) {
							_this.pageFontSize--;
							iframe.contentWindow.document.body.style.fontSize = _this.pageFontSize + 'px';
						}
					});

					this.getAnonid('liseuse_Nav').addEventListener('click', function() {
						if(_this.nav) {
							_this.nav = false;
							nav.style.display = 'none';
						} else {
							_this.nav = true;
							nav.style.display = 'block';
						}
						_this._width = parseInt(_this.getAnonid('liseuse').style.width);
					});
				},

				viewportSize : {
					getHeight : function () {
						return _getSize("Height");
					},
					getWidth : function () {
						return _getSize("Width");
					}
				},

				even : function(v){
					return ((v)%2===0)?false:true;
				},

				fileUdapte : function (_this) {
					var iframe = _this.getAnonid('liseuse_iframe');
					var doc = iframe.contentWindow.document;
					var width = _this.pageWidth;
					var height = iframe.offsetHeight;

					/*
						IE : all 0.01s ease; !!!!!!!
						other : all 0.0001s ease;
					*/
					var style = '<style type="text/css" id="liseuse_style">'
							+'body {'
							+'margin:0px;padding:0px;'
							+'-webkit-transition: all 0.0001s ease;-moz-transition: all 0.0001s ease;-ms-transition: all 0.0001s ease;-o-transition: all 0.0001s ease;transition: all 0.01s linear;'
							+'font-size:'+_this.pageFontSize+'px;'
							+'height:'+height+'px;'
							+'-moz-column-width:'+width+'px;-webkit-column-width:'+width+'px;column-width:'+width+'px;'
							+'-moz-column-gap: 0px;-webkit-column-gap: 0px;column-gap: 0px;'
							+'-moz-hyphens: auto;-webkit-hyphens: auto;-ms-hyphens: auto;-O-hyphens: auto;hyphens: auto;'
							+'}'
							+'#__starttag__ {'
							+'display:none;width:100%;'
							+'}'
							+ 'img {max-height:'+(height-10)+'px !important;max-width:98%;}'
							+'</style>';
					var liseuse_style = doc.getElementById('liseuse_style');
					if(liseuse_style!=undefined)
						liseuse_style.parentNode.removeChild( liseuse_style );

					doc.getElementsByTagName('head')[0].insertAdjacentHTML('beforeend', style);

					var startTagStyle = 'display:none;'
					if(_this.landscape)
						startTagStyle = 'display:block;height:'+height+'px;';
					if(doc.getElementById('__starttag__'))
						doc.getElementById('__starttag__').setAttribute('style',startTagStyle);
				},

				slide : function( newX ) {
					var style = this.getAnonid('liseuse_iframe').contentWindow.document.body.style;
					style[this.prop.transform] = 'translateX(' + newX + 'px)';
					style.fontSize = this.pageFontSize + 'px';
				},

				setMultifiles : function( files, relativeDir, epubName, fixed ) {
					this.options.epubName = epubName;
					this.fixed = fixed;
					this._multifiles = files;
					this._relativeDir = relativeDir;
					this.slider_pages.hide();
					this.slider_files.init(this._multifiles);
					this.notify.pub('liseuse:loadEpub');
				},

				randomLink : function(){
					return '?random='+(new Date()).getTime()+Math.floor(Math.random()*1000000);
				},

				initLinks : function(doc,nav){
					var _this = this;
					[].forEach.call(doc.querySelectorAll('a'), function (element) {
						var href = element.getAttribute('href');
						element.setAttribute('_href',href);
						element.removeAttribute('href');
						element.style.cursor = 'pointer';
						if(href){
							if(href.split(':').length>1) {
								if(!_this.options.externalLink){
									element.addEventListener('click',function(){
										alert(this.getAttribute('_href'));
									})
								} else {
									element.setAttribute('href',href);
									element.setAttribute('target','_blank');
								}
							} else {
								element.addEventListener('click',function(){
									var href = this.getAttribute('_href');
									if(href.substring(0,1)=='#'){// #anchor
											_this.notify.pub('liseuse:link/anchor',[{anchor:href.substring(1,href.length)}]);
									} else { // link and link#anchor
										var link = href;
										_this._gotoAnchor = undefined;
										if(_this._multifiles) {
											href = href.split('#');
											if(href.length>1) {
												link = href[0];
												_this._gotoAnchor = href[1];
											}
											link = link.replace(/\.\.\//g,'');
											link = _this._relativeDir+link;

											_this._multifiles.forEach(function(file,i){
												if(file==link)
													_this.notify.pub('liseuse:link/file',[{file:i}]);
											});
										}
									}
									if(_this.mobile && nav){
										var _event = document.createEvent("HTMLEvents");
										_event.initEvent('click', true, true);
										_this.getAnonid('liseuse_Nav').dispatchEvent(_event);
									}
								});
							}
						}
					});
				},

				initNav : function( content, ncx ) {
					var _this = this;
					var liseuse_nav = this.getAnonid('liseuse_nav');
					this.getAnonid('liseuse_Nav').style.display = 'block';
					liseuse_nav.innerHTML = '';

					if(ncx){
						liseuse_nav.insertComponent('replace',content);
						var navMap = liseuse_nav.querySelector('ncx\\:navMap');
						this.sliderWidth();

						navMap.addEventListener('selectedNavPoint', function (ev) {
							if (ev.detail.id!=null){
								var link = ev.detail.src;
								var href = link;

								_this._gotoAnchor = undefined;
								href = href.split('#');
								if(href.length>1)
									link = href[0];
								link = link.replace(/\.\.\//g,'');
								link = _this._relativeDir+link;

								var actualLink = _this._src;
								if(!_this.fixed && link==actualLink && href[1]!=undefined) {
									_this.slider_pages.change(_this.getPageElement(href[1]));
								} else {
									_this._multifiles.forEach(function(file,i){
										if(file==link){

											if(href[1]!=undefined)
												_this._gotoAnchor = href[1];
											_this.notify.pub('liseuse:link/file',[{file:i}]);


										}
									});
								}
								if(_this.mobile){
									var _event = document.createEvent("HTMLEvents");
									_event.initEvent('click', true, true);
									_this.getAnonid('liseuse_Nav').dispatchEvent(_event);
								}
							}
						}, false);
					} else {
						var ifr = document.createElement('iframe');
						ifr.setAttribute('class','liseuse_iframe_nav');
						liseuse_nav.appendChild(ifr);
						ifr.addEventListener('load', function() {
							if(ifr.getAttribute('src')!=null){
								_this.initLinks(ifr.contentWindow.document,true);
							}
						});
						ifr.src = content;
					}
				},

				getPageElement : function(id) {
					var iframe = this.getAnonid('liseuse_iframe');
					var left = iframe.contentWindow.document.getElementById(id).offsetLeft;
					var style = iframe.contentWindow.document.body.style[this.prop.transform];
					if(style!='')
						left = left - parseFloat(style.split('translateX(')[1].split('px'));
					return Math.abs(parseInt(left / this.pageWidth, 10)) + 1;
				},

				sliderWidth : function(){
					var _this = this;
					setTimeout(function(){
						_this.notify.pub('liseuse:sliderWidth');
						var btWidth = 0;
						[].forEach.call(_this.getAnonid('buttons').querySelectorAll('div'), function (bt) {
							btWidth = btWidth + bt.offsetWidth;
						});
						if(_this.slider_element)
							_this.slider_element.width(_this.getAnonid('tools').offsetWidth - btWidth);
					}, 300);

					if(_waitStart){
						_waitStart = false;
						setTimeout(function(){
							_this.sliderWidth();
						}, 2000);
					}
				},

				refrech : function(){
					// TODO: bug width firefox getComputedStyle ????
					/*
					var random = '?random='+(new Date()).getTime()+Math.floor(Math.random()*1000000);
					this.getAnonid('liseuse_iframe').src = this._src+random;
					*/
				}
			},

			attributes : {
				src : {
					get : function () {
						return this.getAnonid('liseuse_iframe').src;
					},
					set : function (value) {
						this._src = value;
						this.getAnonid('liseuse_iframe').src = value;
					}
				},
				landscape : {
					set : function (value) {
						var _this = this;
						this.landscape = (value === 'true');

						var w = parseInt(this.getAttribute('width'));
						var h = parseInt(this.getAttribute('height'));
						var toogle = function(el,_w,_h){
							if(!_this.fullScreen){
								el.setAttribute('width',_w);
								el.setAttribute('height',_h);
							}
							_this.fileUdapte(_this);
							setTimeout(function(){
								_this.slider_files.reload();
								_this.notify.pub('liseuse:landscape');
							}, 250);
						};
						if(value=='false'){// portrait
							if(w>=h)
								toogle(this,h,w);
							else
								toogle(this,w,h);
						} else {
							if(h>=w)
								toogle(this,h,w);
							else
								toogle(this,w,h);
						}
					}
				},
				width : {
					set : function (value) {
						this._width = parseInt(value);
					}
				},
				height : {
					set : function (value) {
						this._height = parseInt(value);
					}
				}
			},

			properties : {
				_height : {
					set : function (h) {
						var bw = this.getAnonid('bl').offsetWidth;
						var toolsH = this.getAnonid('tools').offsetHeight;

						this.getAnonid('liseuse').style.height = h+'px';
						this.getAnonid('liseuse_nav').style.height = h+'px';

						this.style.height = h+'px';

						var screenHeight = (h-(toolsH+bw));
						this.getAnonid('screen').style.height = screenHeight + 'px';
						this.screenHeight = screenHeight;
						var t = ((h-toolsH + bw)/2);
						this.getAnonid('bl').style.marginTop = t + 'px';
						this.getAnonid('br').style.marginTop = t + 'px';
						var iframe = this.getAnonid('liseuse_iframe');
						iframe.style.height = (screenHeight - 40) + 'px';
						iframe.style.marginTop = 20 +'px';
					}
				},
				_width : {
					set : function (w) {
						var bw = this.getAnonid('bl').offsetWidth;

						this.getAnonid('liseuse').style.width = w+'px';
						if(this.nav)
							this.style.width = (w+450)+'px';
						else
							this.style.width = w+'px';

						var screenWidth = (w-(bw*2));
						this.screenWidth = screenWidth;
						var screen = this.getAnonid('screen');
						screen.style.width = screenWidth + 'px';
						this.getAnonid('tools').style.width = screenWidth + 'px';
						this.getAnonid('v').style.width = screenWidth + 'px';
						this.pageWidth = screenWidth-20;
						var iframe = this.getAnonid('liseuse_iframe');
						iframe.style.width = this.pageWidth + 'px';
						iframe.style.marginLeft = 10 + 'px';
						if(this.landscape)
							this.pageWidth = this.pageWidth/2;

						this.marge = parseInt(this.pageWidth/40);

						this.sliderWidth();
					}
				}
			}
		};

		JSElem.register('http://www.components.org','epubliseuse',epubliseuse);
	}());
});
