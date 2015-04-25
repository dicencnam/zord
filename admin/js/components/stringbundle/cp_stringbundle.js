/**
 * @package  Component
 * @subpackage component (cp_)
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

/**
 * Composants stringbundleset, stringbundle
 * namespace : http://www.components.org
 */
define('cp/stringbundle/cp_stringbundle',function() {
	(function() {

		var cp_stringbundleset = {
			template : '<content/>',

			methods : {
			    domString : function () {
					return '<cp:stringbundleset' + this.attrToString()+ '>' + this.getAnonid('content').innerComponent + '</cp:stringbundleset>';
				},

				getText : function (value) {
					return this.querySelector('cp\\:stringbundle[name="'+value+'"]').innerHTML;
				}
			}
		};

		JSElem.register('http://www.components.org','stringbundleset',cp_stringbundleset);

		var cp_stringbundle = {

			methods : {
			    domString : function () {
					return '<cp:stringbundle' + this.attrToString()+ '></cp:stringbundle>';
				}
			}
		};

		JSElem.register('http://www.components.org','stringbundle',cp_stringbundle);

	}());
});