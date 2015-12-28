
/**
 * teiTohtml demo
 * @author David Dauvergne
 * @copyright 2015 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
define('library/TEITOHTML_demo',function() {

	var depthNCX = 2;

	// --------------------------------------------------------------------------------------------------
	var page = 1;
	var pageOld = 0;
	var medias = [];
	var posNCX = 0;
	var fnc = {

		getPrev : function( elem ) {
			do {
				elem = elem.previousSibling;
			} while ( elem && elem.nodeType !== 1 );
			return elem;
		},
		_attrs : ["alt","class","colspan","dir","xml:lang","height","href","id","lang","role","rowspan","src","style","target","title","width"],

		addClass : function(attr,cls,attrname){
			if(attr[attrname]!=undefined)
				cls.push(attrname+'-'+attr[attrname]);
			return fnc.style(attr,cls);
		},

		src : function(url){
			url = url.replace('etc/','medias/');
			if(medias.indexOf(url)==-1)
				medias.push(url);
			return url;
		},

		href : function(target,creferencing){
			var part = '';
			if(creferencing!=undefined && creferencing!=''){
				part = creferencing.split('/');
				part = part[1]+'.xhtml';
			}
			if(target.lastIndexOf(".")>-1){
				return target;
			} else if(target.lastIndexOf("#")>-1) {
				return part+target;
			} else {
				return part+'#'+target;
			}
		},

		style : function(attr,cl){
			var _t = function(value){
				if(attr.style==undefined)
					attr.style = [];
				attr.style.push(value);
			};

			if(attr.class==undefined)
				attr.class = [];

			for(var key in attr){
				switch (key) {
					case 'rendition':
						switch (attr[key]) {
							case '#left':
								_t('text-align:left;');
							break;
							case '#right':
								_t('text-align:right;');
							break;
							case '#center':
								_t('text-align:center;');
							break;
							default:
								attr.class.push("rendition-"+attr[key].replace('#','').replace(' ','-'));
							break;
						}
					break;
					case 'type':
					case 'ed':
						attr.class.push(key+"-"+attr[key]);
					break;
					case 'xml:id':
						attr.id = attr[key];
					break;
				}
				if(fnc._attrs.indexOf(key)==-1)
					delete attr[key];
			}

			if(cl!=undefined)
				attr.class = cl.concat(attr.class);

			return attr;
		}
	};

	var rules = {

	'text!*' : function(text,value){
		return text.replace(/</g, '&#60;').replace(/>/g,'&#62;').replace(/&/g,'&#38;');
	},
	'text!*|footnotes' : function(text,value){
		return text.replace(/</g, '&#60;').replace(/>/g,'&#62;').replace(/&/g,'&#38;');
	},

	/* --------------------------------- ab --------------------------------- */

		'tei\\:ab' : function(attr){
			attr = fnc.style(attr,['tei-ab']);
			return tag('p',this.apply(),attr);
		},

	/* --------------------------------- add -------------------------------- */

		'tei\\:add' : function(attr){
			attr = fnc.style(attr,['tei-add']);
			return tag('span',this.apply(),attr);
		},


	/* --------------------------------- addrLine --------------------------------- */

		'tei\\:addrLine' : function(attr){
			attr = fnc.style(attr,['tei-addrLine']);
			return tag('p',this.apply(),attr);
		},

	/* --------------------------------- address --------------------------------- */

		'tei\\:address' : function(attr){
			attr = fnc.style(attr,['tei-address']);
			return tag('div',this.apply(),attr);
		},

	/* -------------------------------- argument -------------------------------- */

		'tei\\:argument' : function(attr){
			attr = fnc.style(attr,['tei-argument']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- bibl ---------------------------------- */

		'tei\\:bibl' : function(attr){
			attr = fnc.style(attr,['tei-bibl']);
			return tag('p',this.apply(),attr);
		},

	/* --------------------------------- byline --------------------------------- */

		'tei\\:byline' : function(attr){
			attr = fnc.style(attr,['tei-byline']);
			return tag('div',this.apply(),attr);
		},

	/* --------------------------------- caption --------------------------------- */

		'tei\\:caption' : function(attr){
			attr = fnc.style(attr,['tei-caption']);
			return tag('p',this.apply(),attr);
		},

	/* ---------------------------------- castitem  ---------------------------------- */

		'tei\\:castitem ' : function(attr){
			attr = fnc.style(attr,['tei-castitem ']);
			return tag('li',this.apply(),attr);
		},

	/* ---------------------------------- castlist  ---------------------------------- */

		'tei\\:castlist ' : function(attr){
			attr = fnc.style(attr,['tei-castlist ']);
			return tag('ul',this.apply(),attr);
		},

	/* ---------------------------------- cell ---------------------------------- */

		'tei\\:cell' : function(attr,value){
			if(attr['cols']){
				if(attr['cols']>1)
					attr.colspan = attr['cols'];
				delete attr['cols'];
			}
			if(attr['rows']){
				if(attr['rows']>1)
					attr.rowspan = attr['rows'];
				delete attr['rows'];
			}
			attr = fnc.style(attr);
			return tag('td',this.apply(),attr);
		},

	/* --------------------------------- closer --------------------------------- */

		'tei\\:closer' : function(attr){
			attr = fnc.style(attr,['tei-closer']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- date ---------------------------------- */

		'tei\\:date' : function(attr){
			attr = fnc.style(attr,['tei-date']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- dateline ---------------------------------- */

		'tei\\:dateline' : function(attr){
			return this.apply();
		},

	/* ---------------------------------- desc ---------------------------------- */

		'tei\\:desc' : function(attr){
			attr = fnc.style(attr,['tei-desc']);
			return tag('p',this.apply(),attr);
		},

	/* ---------------------------------- div ---------------------------------- */

		'tei\\:div' : function(attr){
			attr = fnc.style(attr,['tei-div']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- docAuthor ---------------------------------- */

		'tei\\:docAuthor' : function(attr){
			attr = fnc.style(attr,['tei-docAuthor']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- docEdition ---------------------------------- */

		'tei\\:docEdition' : function(attr){
			attr = fnc.style(attr,['tei-docEdition']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- docImprint ---------------------------------- */

		'tei\\:docImprint' : function(attr){
			attr = fnc.style(attr,['tei-docImprint']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- docTitle ---------------------------------- */

		'tei\\:docTitle' : function(attr){
			attr = fnc.style(attr,['tei-docTitle']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- emph ---------------------------------- */

		'tei\\:emph' : function(attr){
			attr = fnc.style(attr,['tei-emph']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- epigraph ---------------------------------- */

		'tei\\:epigraph' : function(attr){
			attr = fnc.style(attr,['tei-epigraph']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- facsimile ---------------------------------- */

		'tei\\:facsimile' : function(attr){
			attr = fnc.style(attr,['tei-facsimile']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- figure ---------------------------------- */

		'tei\\:figure' : function(attr){
			attr = fnc.addClass(attr,['tei-figure'],'rend');
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- floatingText ---------------------------------- */

		'tei\\:floatingText' : function(attr){
			attr = fnc.style(attr,['tei-floatingText']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- foreign ---------------------------------- */

		'tei\\:foreign' : function(attr){
			attr = fnc.style(attr,['tei-foreign']);
			return tag('span',this.apply(),attr);
		},

	/* --------------------------------- forename -------------------------------- */

		'tei\\:forename' : function(attr){
			attr = fnc.style(attr,['tei-forename']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- fw ---------------------------------- */

		'tei\\:fw' : function(){
			return this.apply();
		},

	/* ---------------------------------- gloss ---------------------------------- */

		'tei\\:gloss' : function(attr){
			attr = fnc.style(attr,['tei-gloss']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- graphic ---------------------------------- */

		'tei\\:graphic' : function(attr){
			attr.src = fnc.src(attr.url);
			attr = fnc.style(attr,['tei-graphic']);
			attr.alt = '';
			return emptyTag('img',attr);
		},

	/* ---------------------------------- head ---------------------------------- */

		'tei\\:head' : function(attr){
			attr = fnc.style(attr,['tei-head']);
			return tag('p',this.apply(),attr);
		},

	/* ---------------------------------- hi ---------------------------------- */

		'tei\\:hi' : function(attr){
			var hi = attr.rend;
			switch (hi) {
				case 'sup':
				case 'sub':
				case 'b':
				case 'i':
					attr = fnc.style(attr);
					return tag(hi,this.apply(),attr);
				break;
				default:
					attr = fnc.addClass(attr,[],'rend');
					return tag('span',this.apply(),attr);
			}
		},

	/* ---------------------------------- item ---------------------------------- */

		'tei\\:item' : function(attr){
			var n = attr.n;
			attr = fnc.style(attr,['tei-item']);
			var before = '<span class="tei-item-before">– </span>';
			if(n!=undefined)
				before = '<span class="tei-item-before">'+n+' </span>';
			return tag('li','<span class="tei-item">– </span>'+this.apply(),attr);
		},

	/* ---------------------------------- l ---------------------------------- */

		'tei\\:l' : function(attr){
			attr = fnc.style(attr,['tei-l']);
			return tag('p',this.apply(),attr);
		},

	/* ---------------------------------- label ---------------------------------- */

		'tei\\:label' : function(attr){
			attr = fnc.style(attr,['tei-label']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- lb ---------------------------------- */

		'tei\\:lb' : function(attr){
			attr = fnc.style(attr,['tei-lb']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- lg ---------------------------------- */

		'tei\\:lg' : function(attr){
			attr = fnc.style(attr,['tei-lg']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- list ---------------------------------- */

		'tei\\:list' : function(attr){
			var element = 'ul';
			if(attr.type!=undefined && attr.type!='ordered')
				element = 'ol';

			attr = fnc.style(attr,['tei-list']);
			return tag(element,this.apply(),attr);
		},

	/* ---------------------------------- listBibl ---------------------------------- */

		'tei\\:listBibl' : function(attr){
			attr = fnc.style(attr,['tei-listBibl']);
			return tag('div',this.apply(),attr);
		},

	/* --------------------------------- name -------------------------------- */

		'tei\\:name' : function(attr){
			attr = fnc.style(attr,['tei-name']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- note ---------------------------------- */

		'tei\\:note' : function(attr){
			var a = {};
			var place = this.getAttribute('place');
			var n = this.getAttribute('n');
			if(place!=undefined && (place=="foot" || place=="end") && n!=undefined){
				a.href = '#footref_'+attr.id;
			}
			attr = fnc.style(attr,['tei-note']);
			return tag('sup',tag('a',n,a),attr);
		},

	/* --------------------------------- opener ---------------------------------- */

		'tei\\:opener' : function(attr){
			attr = fnc.style(attr,['tei-opener']);
			return tag('p',this.apply(),attr);
		},

	/* --------------------------------- postscript ---------------------------------- */

		'tei\\:postscript' : function(attr){
			attr = fnc.style(attr,['tei-postscript']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- p ---------------------------------- */

		'tei\\:p' : function(attr){
			attr = fnc.style(attr,['tei-p']);
			return tag('p',this.apply(),attr);
		},

	/* ---------------------------------- pb ---------------------------------- */

		'tei\\:pb' : function(attr){
			var ed = attr.ed;
			var n = attr.n;
			attr = fnc.style(attr,['tei-pb']);
			if(attr.ed!=undefined)
				return tag('span','['+n+']',attr);

			return tag('span','{p. '+n+'}',attr);
		},

	/* ---------------------------------- quote ---------------------------------- */

		'tei\\:quote' : function(attr){
			attr = fnc.style(attr,['tei-quote']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- ref ---------------------------------- */

		'tei\\:ref' : function(attr){
			attr.href = fnc.href(attr.target,attr.creferencing);
			attr = fnc.style(attr,['tei-ref']);
			return tag('a',this.apply(),attr);
		},


	/* ---------------------------------- role ---------------------------------- */

		'tei\\:role' : function(attr){
			attr = fnc.style(attr,['tei-role']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- roledesc ---------------------------------- */

		'tei\\:roledesc' : function(attr){
			attr = fnc.style(attr,['tei-roledesc']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- row ---------------------------------- */

		'tei\\:row' : function(attr,value){
			return tag('tr',this.apply(),attr);
		},

	/* --------------------------------- salute -------------------------------- */

		'tei\\:salute' : function(attr){
			attr = fnc.style(attr,['tei-salute']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- seg ---------------------------------- */

		'tei\\:seg' : function(attr){
			attr = fnc.style(attr,['tei-seg']);
			return tag('span',this.apply(),attr);
		},

	/* --------------------------------- signed -------------------------------- */

		'tei\\:signed' : function(attr){
			attr = fnc.style(attr,['tei-signed']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- sp ---------------------------------- */

		'tei\\:sp' : function(attr){
			attr = fnc.style(attr,['tei-sp']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- space ---------------------------------- */

		'tei\\:space' : function(attr){
			attr = fnc.style(attr,['tei-space']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- speaker ---------------------------------- */

		'tei\\:speaker' : function(attr){
			attr = fnc.style(attr,['tei-speaker']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- stage ---------------------------------- */

		'tei\\:stage' : function(attr){
			attr = fnc.style(attr,['tei-stage']);
			return tag('span',this.apply(),attr);
		},

	/* ---------------------------------- table ---------------------------------- */

		'tei\\:table' : function(attr,value){
			return tag('table',this.apply(),attr);
		},
		'tei\\:table.elm > tei\\:head' : function(attr,value){
			return tag('caption',this.apply(),attr);
		},

	/* ---------------------------------- tei ---------------------------------- */

		'tei\\:tei' : function(attr){
			attr = fnc.style(attr,['tei-tei']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- titlePage ---------------------------------- */

		'tei\\:titlePage' : function(attr){
			attr = fnc.style(attr,['tei-titlePage']);
			return tag('div',this.apply(),attr);
		},

	/* ---------------------------------- titlePart ---------------------------------- */

		'tei\\:titlePart' : function(attr){
			attr = fnc.style(attr,['tei-titlePart']);
			return tag('p',this.apply(),attr);
		},

	/* -------------------------------------------------------------------------------- */
	/* -------------------------------- mode footnotes -------------------------------- */

		'tei\\:tei|footnotes' : function(attr){
			return this.apply({select:'tei\\:pb[n],tei\\:note',mode:'footnotes'});
		},

		'tei\\:pb[n]|footnotes' : function(attr){
				var n = this.getAttribute('n');
				var ed = this.getAttribute('ed');
				if(n!='undefined' && ed!='temoin')
					page = n;
			return '';
		},

		'tei\\:note|footnotes' : function(attr){
			var beforeHTML = '';
			if(pageOld!=page){
				beforeHTML = '<div class="foot-page">p. '+page+'</div>';
				pageOld = page;
			}
			var place = this.getAttribute('place');
			var n = this.getAttribute('n');
			if(place!=undefined && (place=="foot" || place=="end") && n!=undefined){

				var clnotnote = '';
				if(attr.__position__!=undefined)
					clnotnote = ' class="notesdouble"'
				var content = '<div id="footref_'+attr.id+
					'"'+clnotnote+'><div class="footnote-counter">'+'<a href="#'+attr.id+'">'+n+'</a>'+
					'</div><div class="footnote-note">'+this.apply()+'</div></div>';

				return beforeHTML+content;
			}
			return '';
		}
	};

	var rulesNCX = {
		'text!*' : function(text,value){
			return text.replace(/</g, '&#60;').replace(/>/g,'&#62;').replace(/&/g,'&#38;');
		},
		'ul' : function(attr,value){
			var uls = this.counterParent('ul');
			if(uls<=depthNCX)
				return this.apply();
			else
				return '';
			},
		'li' : function(attr,value){
			posNCX++;
			var pos = posNCX;
			var file = attr['data-part'].split('/');
			file = file[1]+'.xhtml';
			if(attr['data-id']!=undefined && attr['data-id']!='')
				file = file+'#'+attr['data-id'];
			return tag('navPoint',
			this.apply({select:':scope > span '})+emptyTag('content',{src:file})+this.apply({select:':scope > ul '}),{id:'n-'+pos,playOrder:pos})+N;
		},
		'span' : function(){
			return tag('navLabel',tag('text',this.apply()));
		}
	};

	var teitohtml = {
		initBook : function(){
			medias = [];
		},
		getTei : function(source,title){
			page = 1;
			pageOld = 0;
			// création d'un élément
			var teiEl = XSLDom.init(source);

			// footnote ----------------------------------------------------------
			[].forEach.call(teiEl.querySelectorAll('tei\\:note tei\\:note'), function (el,i) {
				el.setAttribute('__position__',"note-note");
			});

			return multiline([
				'<?xml version="1.0" encoding="UTF-8" ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
				'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">',
				'<head>',
				'<title>'+title+'</title>',
				'<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8"/>',
				'<link href="css/epub.css" rel="stylesheet" type="text/css"></link>',
				'</head>',
				'<body>',
				XSLDom(teiEl,rules),
				'<div id="footnotes">',
				XSLDom(teiEl,rules,{mode:'footnotes'}),
				'</div>',
				'</body>',
				'</html>'
			]);
		},

		getNavMap : function(source){
			posNCX = 0;
			// création d'un élément
			var ncxEl = XSLDom.init(source);
			var front = ncxEl.querySelector('#tocTEI_front');
			var body = ncxEl.querySelector('#tocTEI_body');
			var back = ncxEl.querySelector('#tocTEI_back');
			var xml = multiline([
				'<navMap>',
				XSLDom(front,rulesNCX),
				XSLDom(body,rulesNCX),
				XSLDom(back,rulesNCX),
				'</navMap>',
			]);

			var reg = /(>)\s*(<)(\/*)/g;
			xml = xml.replace(/\r|\n/g, '');
			xml = xml.replace(reg, '$1\r\n$2$3');
			return xml;
		},

		getMedias : function(){
			return medias;
		}
	};

return teitohtml;
});
