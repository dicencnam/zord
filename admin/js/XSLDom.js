/**
* XSLDom
* @version 0.1
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
(function() {

	/**
	* Apply-templates method (<xsl:apply-templates/>) all Elements
	* @method apply
	* @param {object} args Arguments (select,mode and value)
	* @return {string}
	*/
	window.Element.prototype.apply = function(args) {
		if(args==undefined)
			return _parseDOM(this);// <xsl:apply-templates/>
		else if(args.select!=undefined)// <xsl:apply-templates select="xx"/> and <xsl:apply-templates select="xx" mode="yy"/>
			return _select(this,args.select,args.mode,args.value);
		else // <xsl:apply-templates mode="yy"/>
			return _parseDOM(this,args.mode,args.value);
	};

	/**
	* DOM parser
	* @method _parseDOM
	* @param {dom} node Element parse
	* @param {string} mode Filter mode
	* @param {object} value Options values
	* @return {string}
	*/
	var _parseDOM = function (node,mode,value) {
		var string = '';
		[].forEach.call(node.childNodes, function(child,index) {
			string += _apply(child,mode,value,index);
		});
		return string;
	};

	/**
	* Select attribute (<xsl:apply-templates select="xx"/>)
	* @method _select
	* @param {dom} element Element to apply select
	* @param {string} selector CSS selector
	* @param {string} mode Filter mode
	* @param {object} value Options values
	* @return {string}
	*/
	var _select = function (element,selector,mode,value) {
		var string = '';
		[].forEach.call(element.querySelectorAll(selector), function (el,index) {
			string += _apply(el,mode,value,index);
		});
		return string;
	};

	/**
	* Apply-templates private function (<xsl:apply-templates/>)
	* @param {dom} element Element to parse
	* @param {string} mode Filter mode
	* @param {object} value Options values
	* @return {string}
	*/
	var _apply =function(element,mode,value,index){
		var string = '';
		// todo function index
		// var index = Array.prototype.indexOf.call(element.parentNode.childNodes, element));
		if (element.nodeValue==null && element.XSLDom_apply_tag){
			if(mode==undefined)
				string += element.XSLDom_apply_tag._.call(element, _getAttributes(element),value);
			else if(element.XSLDom_apply_tag[mode]!=undefined)
				string += element.XSLDom_apply_tag[mode].call(element, _getAttributes(element),value);
		} else if(element.nodeValue==null){
			string += element.apply({mode:mode,value:value});
		} else {
			if(element.parentNode.XSLDom_apply_text){
				if(mode==undefined)
					string += element.parentNode.XSLDom_apply_text._.call(element, element.nodeValue,value);
				else
					string += element.parentNode.XSLDom_apply_text[mode].call(element, element.nodeValue,value);
			} else {
				string += element.nodeValue;
			}
		}
		return string;
	};

	/**
	* Get attibutes from element
	* @param {dom} element Element to parse
	* @return {object}
	*/
	var _getAttributes = function(element){
		var attributes = {};
		if (element.attributes.length > 0) {
			[].forEach.call(element.attributes, function(att) {
				attributes[att.nodeName] = att.value;
			});
		}
		return attributes;
	};

	/**
	* Counter parent node
	* @param {string} nodeName Node name to count
	* @return {integer}
	*/
	var _counterParent = function(nodeName){
		var elem = this;
		var counter = 0;
		while ( elem && elem.id !== "__root__" ){
			elem = elem.parentNode;
			if(elem && elem.nodeName.toLowerCase()==nodeName)
				counter++;
		}
		return counter;
	};

	/**
	* XSLDom
	* @param {dom} element Element to parse
	* @param {object} rules Rules
	* @param {object} args Arguments (select,mode and value)
	* @return {string}
	*/
	window.XSLDom = function(element,rules,args){
		// delete XSLDom_apply_tag method if exist
		[].forEach.call(element.querySelectorAll('*'), function (el) {
			if(el.XSLDom_apply_tag)
				delete el.XSLDom_apply_tag;
			if(el.XSLDom_apply_text)
				delete el.XSLDom_apply_text;
			if(el.counterParent==undefined)
				el.counterParent = _counterParent;
		});

		/* create rules object compatible (explode selector + mode  : "selector|mode'")
			'.cl' : function(){...},// fnc 1
			'.cl|modeX' : function(){...}// fnc 2

			translate to :
			'.cl : {'
				'_' : function(){...},// fnc 1
				'modeX' : function(){...}// fnc 2
			}
		*/
		var R = {};
		for(var key in rules){
			var rule = ruleParse(key);

			if(R[rule.type]==undefined)
				R[rule.type] = {};

			if(R[rule.type][rule.selector]==undefined)
				R[rule.type][rule.selector] = {};

			if(rule.mode!=undefined)
				R[rule.type][rule.selector][rule.mode] = rules[key];
			else
				R[rule.type][rule.selector]['_'] = rules[key];
		}

		// associate element and rules
		for(var type in R){
			for(var selector in R[type]){
				[].forEach.call(element.querySelectorAll(selector), function (el) {

					if(el['XSLDom_apply_'+type]==undefined)
						el['XSLDom_apply_'+type] = {};

					for(var fnc in R[type][selector])
						el['XSLDom_apply_'+type][fnc] = R[type][selector][fnc];
				});
			}
		}
		// apply template
		return element.apply(args);
	};

	var ruleParse = function(rule){
		var selector = ''
		var mode = undefined;
		var type = 'tag';

		var x = rule.split('!');
		if(x.length>1){
			type = x[0];
			rule = x[1];
		}
		var x = rule.split('|');
		if(x.length>1)
			mode = x[1];

		selector = x[0];

		return {
			selector : selector,
			mode : mode,
			type : type
		}
	};

	/**
	* XSLDom initialisation
	* @param {string} source Source
	* @return {dom}
	*/
	window.XSLDom.init = function(source) {
		var element = document.createElement('div');
		element.id = '__root__';
		element.innerHTML = source;
		return element;
	}

}());
