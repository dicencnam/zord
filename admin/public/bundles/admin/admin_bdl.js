/**
 * Bundle appli
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
define('bdl/admin/admin_bdl',function() {
	var bundles = {
		'appli' : {
			components : { // liste des composants nécessaire pour ce module
				stringbundle : ['cp/stringbundle/cp_stringbundle'],
				popupboxes : ['cp/popupboxes/cp_popupboxes','css!cp/popupboxes/cp_popupboxes']
			},
			tpl : 'tpl!bdl/admin/admin',// le fichier de template
			locale : 'i18n!bdl/admin/locale/admin!default:fr-FR',// fichier des locales du template
			init : ['bdl/admin/admin']
			// css : 'css!bdl/appli/appli'// css pour le template
		},
		'source' : {
			components : {},
			tpl : 'tpl!bdl/source/source',// le fichier de template
			locale : 'i18n!bdl/source/locale/source!default:fr-FR',// fichier des locales du template
			init : ['bdl/source/source','library/zip'],
			css : ['css!bdl/source/source','css!bdl/source/tei'],// css pour le template
		},
		'publication' : {
			components : {},
			tpl : 'tpl!bdl/publication/publication',
			locale : 'i18n!bdl/publication/locale/publication!default:fr-FR',
			init : 'bdl/publication/publication',
			css : 'css!bdl/publication/publication'
		},
		'cache' : {
			components : {},
			tpl : 'tpl!bdl/cache/cache',
			locale : 'i18n!bdl/cache/locale/cache!default:fr-FR',
			init : 'bdl/cache/cache',
			//css : 'css!bdl/cache/cache'
		},
		'portal' : {
			components : {},
			tpl : 'tpl!bdl/portal/portal',
			locale : 'i18n!bdl/portal/locale/portal!default:fr-FR',
			init : 'bdl/portal/portal',
			//css : 'css!bdl/portal/portal'
		},
		'users' : {
			components : {},
			tpl : 'tpl!bdl/users/users',
			locale : 'i18n!bdl/users/locale/users!default:fr-FR',
			init : 'bdl/users/users',
			css : 'css!bdl/users/users'
		},
		'counter' : {
			components : {},
			tpl : 'tpl!bdl/counter/counter',
			locale : 'i18n!bdl/counter/locale/counter!default:fr-FR',
			init : 'bdl/counter/counter',
			css : 'css!bdl/counter/counter'
		},
		'epub' : {
			components : {},
			tpl : 'tpl!bdl/epub/epub',
			locale : 'i18n!bdl/epub/locale/epub!default:fr-FR',
			init : ['library/XMLWrite','library/XSLDom','bdl/epub/epub'],
			css : 'css!bdl/epub/epub'
		}
	};
	return bundles;
});
