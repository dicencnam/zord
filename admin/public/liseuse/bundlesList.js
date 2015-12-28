/**
 * @package Liseuse
 * @author David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

/**
 * BundlesList Liseuse
 */
define('../public/liseuse/bundlesList.js',function() {
	var bundles = {

		'Liseuse' : {
			componentsNamespace : {
				'opf' : 'http://www.idpf.org/2007/opf',
				'ncx' : 'http://www.daisy.org/z3986/2005/ncx/'
			},
			components : { // liste des composants nécessaire pour ce module
				ncxNavmap : ['cp/ncx/navmap/ap_navmap2','css!cp/ncx/navmap/ap_navmap2'],
				epubliseuse : ['cp/epubliseuse/lis_epubliseuse','cp/epubliseuse/lis_notify','css!cp/epubliseuse/lis_epubliseuse'],
				epubopf : ['cp/opf/epubopf/ap_epubopf','css!cp/opf/epubopf/ap_epubopf'],
				opfSpine : ['cp/opf/spine/ap_spine','css!cp/opf/spine/ap_spine'],
				opfItemref : ['cp/opf/itemref/ap_itemref','css!cp/opf/itemref/ap_itemref'],
				barscroll : ['cp/barscroll/cp_barscroll','css!cp/barscroll/cp_barscroll'],
				scrollbarthumb : ['cp/barscroll/cp_scrollbarThumb','css!cp/barscroll/cp_scrollbarThumb'],
			},
			tpl : 'tpl!bdl/Liseuse/liseuse',// le fichier de template
			//locale : 'i18n!bdl/Liseuse/locale/liseuse!default:fr-FR',// fichier des locales du template
			init : ['bdl/Liseuse/liseuse'],// fichier js d'initialisation
			css : 'css!bdl/Liseuse/liseuse'// css pour le template
		}
	};
	return bundles;
});
