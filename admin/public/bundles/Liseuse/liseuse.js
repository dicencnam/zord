/**
 * @package Liseuse
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

/**
 * Bundle liseuse
 */
define('bdl/Liseuse/liseuse',function() {

	var queryToObject = function(str, seperator) {
		var ret = {};
		var qp = str.split(seperator || "&");
		var dec = decodeURIComponent;
		qp.forEach(function(item) {
			if (item.length) {
				var parts = item.split("=");
				ret[dec(parts.shift())] = dec(parts.join("="));
			}
		});
		return ret;
	};

	var loadXML = function(file){
		var request = new XMLHttpRequest();
		request.open("GET", file, false);
		request.send(null);
		return request.responseXML;
	};

	var configEpub = function(_config,epubDir){
		_config.epub = epubDir;
		_config.epubPathEpub = _config.epubsPath+_config.epub+'/';
		_config.containerFile = _config.epubPathEpub+'META-INF/container.xml';
		return _config;
	};

	var transformXSLT = function(xmlFile,xsltFile){
		if(window.ActiveXObject !== undefined){ //ie
			var xslt = new ActiveXObject("Msxml2.XSLTemplate.3.0");
			var xslDoc = new ActiveXObject("Msxml2.FreeThreadedDOMDocument.3.0");
			var xslProc;
			xslDoc.async = false;
			xslDoc.load(xsltFile);
				 xslt.stylesheet = xslDoc;
				 var xmlDoc = new ActiveXObject("Msxml2.DOMDocument.3.0");
				 xmlDoc.async = false;
				 xmlDoc.load(xmlFile);
			xslProc = xslt.createProcessor();
			xslProc.input = xmlDoc;
			// xslProc.addParameter("param1", "Hello");
			xslProc.transform();
			return xslProc.output;
		} else {
			var xml = loadXML(xmlFile);
			var xsl = loadXML(xsltFile);
			var xsltProcessor = new XSLTProcessor();
			xsltProcessor.importStylesheet(xsl);
			return xsltProcessor.transformToDocument(xml);
		}
	};

	// ------------------------------------------------------------------
	var config = null;
	var LiseuseView = {
		init : function (_config) {

			config = _config;

			window._Msg = document.getElementById('msg');

			epubliseuse.init(_config.options);

			if(_config.autoload){
				// query epub
				var searchQuery = queryToObject(window.location.search.substring(1));
				var epub = _config.epubNotFound;
				if(_config.epub==undefined) {
					if(searchQuery.epub!=undefined)
						epub = searchQuery.epub;
				} else {
					epub = _config.epub;
				}

				LiseuseView.loadEpub(epub);
			}
		},
		loadEpub : function(epub){
			// elements
			var epubopf = document.getElementById('epubopf');
			var book_doc_spine = document.getElementById('book_doc_spine');
			var epubliseuse = document.getElementById('epubliseuse');
			var container = document.getElementById('book_doc_container');

			var _config = configEpub(config,epub);

			// get container to find OPF file
			var containerXML = loadXML(_config.containerFile);
			if(containerXML==null) {
				_config = configEpub(config,_config.epubNotFound);
				containerXML = loadXML(_config.containerFile);
			}

			var opfLink = containerXML.querySelector('rootfile').getAttribute('full-path');
			var _s = opfLink.split('/');
			_config.opfFile = _s.pop();
			_config.opfDir = _config.epubPathEpub+_s.join('/')+'/';
			_config.opfLink = _config.epubPathEpub+opfLink;

			// get OPF file
			var opfDocument = transformXSLT(_config.opfLink,_config.liseusePath+'lib/prefixopf.xsl');
			if(window.ActiveXObject !== undefined)
				var opfString = opfDocument.replace(/<\?.*\?>/,'');
			else
				var opfString = new XMLSerializer().serializeToString(opfDocument.documentElement);

			var opfString = opfString.replace(/<opf\:(\w+)\s([^>]*)(\/>)/g,'<opf:$1 $2></opf:$1>');

			// opf
			epubopf.load('id', opfString, '', _config.opfDir, _config.opfFile);

			// spine
			book_doc_spine.insertComponent('replace',epubopf.getSpine());
			var spine = book_doc_spine.querySelector('opf\\:spine');
			spine.init(epubopf);

			var _files = [];
			spine.getFiles(true).forEach(function(file,i){
				_files.push(_config.opfDir+file);
			});

			// NCX
			// NCX
			var objNCX = epubopf.getNcx();

			if(objNCX.href!=null) {
				var ncxDocument = transformXSLT(_config.opfDir+objNCX.href,_config.liseusePath+'lib/prefixncx.xsl');
				if(window.ActiveXObject !== undefined){
					var navMapString = ncxDocument.replace(/<\?.*\?>/,'');
					var div = document.createElement('div');
					div.innerHTML = navMapString;
					var navMap = div.querySelector('ncx\\:navmap');
				} else {
					var navMap = ncxDocument.documentElement.querySelector('navMap');
				}
				if(navMap) {
					var navMapString = new XMLSerializer().serializeToString(navMap);
					epubliseuse.initNav(navMapString,true);
				}
			} else {
				var objNav = epubopf.getNav();
				if(objNav.href) {
					var ncxDocument = _config.opfDir+objNav.href;
					epubliseuse.initNav(ncxDocument,false);
				}
			}

			// fixed layout and options
			var isFixed = false;
			if(epubopf.isFixedLayout() && _config.options.fixed==undefined)
				isFixed = true;

			epubliseuse.setMultifiles(_files,_config.opfDir,_config.epub,isFixed);
		}
	};
	return LiseuseView;
});
