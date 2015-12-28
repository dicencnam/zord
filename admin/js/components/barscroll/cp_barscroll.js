/**
 * @package  Component
 * @subpackage component (cp_)
 * @author David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

/**
 * Composant barscroll
 * namespace : http://www.components.org
 */
define('cp/barscroll/cp_barscroll',function() {
	(function() {

		var cp_barscroll = {

			template : '<span anonid="left" class="barscrollbleft">◄</span><span anonid="right" class="barscrollbright">►</span><div anonid="cc" class="barscrollcc"><content class="barscrollcontent"/></div>',

			methods : {
			    domString : function () {
					return '<cp:barscroll' + this.attrToString()+ '>'+this.innerComponent+'</cp:barscroll>';
				},

				clear : function () {
					this.getAnonid('content').innerHTML = '';
				},

				domInsert : function () {
					var _this = this;
					var timer;
					var right = this.getAnonid('right');
					var left = this.getAnonid('left');
					var content = this.getAnonid('cc');
					setTimeout(function() {
						content.scrollLeft = 0;
					}, 500);

					var scroll = function (v,t) {
						content.scrollLeft -= v;
						if(t)
							timer = setTimeout(function(){scroll(v,true)}, 10);
					};

					var mouseWheelEvt = function (event){
						event.preventDefault();
						if ((event.wheelDelta || event.detail) > 0)
							scroll(-30,false);
						else
							scroll(30,false);
						return false;
					}
					if (typeof window.ontouchstart !== 'undefined') {
						content.style.overflowX = "scroll";
						right.style.display = 'none';
						left.style.display = 'none';
					} else {
						if ("onmousewheel" in this)
							this.onmousewheel = mouseWheelEvt;
						else
							this.addEventListener("DOMMouseScroll", mouseWheelEvt);
					}

					right.addEventListener("mousedown", function(evt){
						scroll(-8,true);
					});
					right.addEventListener("mouseup", function(evt){
						clearTimeout(timer);
					});
					left.addEventListener("mousedown", function(evt){
						scroll(8,true);
					});
					left.addEventListener("mouseup", function(evt){
						clearTimeout(timer);
					});
				}
			}
		};

		JSElem.register('http://www.components.org','barscroll',cp_barscroll);

	}());
});