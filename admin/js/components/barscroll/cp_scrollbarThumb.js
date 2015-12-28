/**
 * @package  Component
 * @subpackage component (cp_)
 * @author David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

/**
 * Composant scrollbarThumb
 * namespace : http://www.components.org
 */
define('cp/scrollbar/cp_scrollbarThumb',function() {
	(function() {

		var cp_scrollbarThumb = {

			template : '<img anonid="img"/>',

			methods : {
				init : function ( index ) {
					this.elementIndex = index;
				}
			},

			attributes : {

				src : {
					set : function (value) {
						this.getAnonid('img').setAttribute('src',value);
					}
				}
			}
		};
		JSElem.register('http://www.components.org','scrollbarthumb',cp_scrollbarThumb);
	}());
});