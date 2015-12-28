/**
 * @package  Component
 * @author David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

/**
 * Composants ncx - navmap, navpoint, navlabel, text, content
 * namespace : http://www.daisy.org/z3986/2005/ncx/
 */
define('cp/ncx/navmap/ap_navmap', function() {
	(function() {
		var _dom = 0;
		// --------------------------------- navMap
		var ncx_navmap = {

			template: '<content/>',

			methods: {
				domString: function(val) {
					_dom = val;
					this.updatePlayorder();
					if(_dom==0)
						return '<navMap' + this.attrToString() + '>' + this.getAnonid('content').innerComponent + '</navMap>';
					else
						return '<ncx:navMap' + this.attrToString() + '>' + this.getAnonid('content').innerComponent + '</ncx:navMap>';
				},

				updatePlayorder: function() {
					[].forEach.call(this.querySelectorAll('ncx\\:navPoint'), function(element, i) {
						element.setAttribute('playorder', i + 1);
					});
				}
			},

			events: {
				selectedNavPoint: function(ev) {
					ev.stopPropagation();
					[].forEach.call(this.querySelectorAll('ncx\\:navPoint[selected="true"]'), function(element) {
						element.removeAttribute('selected');
					});

					if (ev.detail.id != null){
						ev.target.setAttribute('selected', 'true');
						ev.detail.src = ev.target.querySelector('ncx\\:content').getAttribute('src');
					}

					this.selectedNavPoint = ev.detail.id;
				}
			}
		};

		JSElem.register('http://www.daisy.org/z3986/2005/ncx/', 'navmap', ncx_navmap);

		// --------------------------------- navPoint
		var ncx_navpoint = {

			template: '<content/>',

			methods: {
				domString: function() {
					if(_dom==0) {
						var attr = this.attrToString('target', 'selected').replace(/playorder/g,'playOrder');
						return '<navPoint' + attr + '>' + this.getAnonid('content').innerComponent + '</navPoint>';
					} else {
						return '<ncx:navPoint' + this.attrToString('selected') + '>' + this.getAnonid('content').innerComponent + '</ncx:navPoint>';
					}
				}
			},

			events: {

				click: function(ev) {
					ev.stopPropagation();
					var id = this.getAttribute('id');
					var target = this.getAttribute('target');
					var event = document.createEvent("CustomEvent");
					event.initCustomEvent('selectedNavPoint', true, true, {
						id: id,
						target : target
					});
					this.dispatchEvent(event);
				}
			}
		};

		JSElem.register('http://www.daisy.org/z3986/2005/ncx/', 'navpoint', ncx_navpoint);

		// --------------------------------- navLabel
		var ncx_navlabel = {

			template: '<content/>',

			methods: {
				domString: function() {
					if(_dom==0)
						return '<navLabel' + this.attrToString() + '>' + this.getAnonid('content').innerComponent + '</navLabel>';
					else
						return '<ncx:navLabel' + this.attrToString() + '>' + this.getAnonid('content').innerComponent + '</ncx:navLabel>';
				}
			}
		};

		JSElem.register('http://www.daisy.org/z3986/2005/ncx/', 'navlabel', ncx_navlabel);

		// --------------------------------- text
		var ncx_text = {

			template: '<content/>',

			methods: {
				domString: function() {
					if(_dom==0)
						return '<text' + this.attrToString() + '>' + this.getAnonid('content').innerComponent + '</text>';
					else
						return '<ncx:text' + this.attrToString() + '>' + this.getAnonid('content').innerComponent + '</ncx:text>';
				}
			}
		};

		JSElem.register('http://www.daisy.org/z3986/2005/ncx/', 'text', ncx_text);

		// --------------------------------- content
		var ncx_content = {

			template: '<div class="ncxsrc" anonid="src"></div>',

			methods: {
				domString: function() {
					if(_dom==0)
						return '<content' + this.attrToString() + '></content>';
					else
						return '<ncx:content' + this.attrToString() + '></ncx:content>';
				}
			},

			attributes: {
				src: {
					set: function(value) {
						this.getAnonid('src').innerHTML = value;
					}
				}
			}
		};

		JSElem.register('http://www.daisy.org/z3986/2005/ncx/', 'content', ncx_content);
	}());
});