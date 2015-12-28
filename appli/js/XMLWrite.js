/**
* XMLWrite
* @version 0.1
* @author David Dauvergne
* @copyright 2015 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
(function() {

	/*
		Constants
	*/
	window.N = '\n';
	window.NN = '\n\n';
	window.NNN = '\n\n\n';

	window.T = '\t';
	window.TT = '\t\t';
	window.TTT = '\t\t\t';

	/**
	* Tag create
	* @method tag
	* @param {string} name Tag name
	* @param {string} content Tag content
	* @param {object} attrs Attributes
	* @param {boolean} trim Trim content
	* @return {string}
	*/
	window.tag = function(name,content,attrs,trim) {
		if(content==undefined)
			content = '';
		if(trim)
			content = content.trim();
		var _attr = '';
		if(attrs!=undefined)
			for(var key in attrs)
				_attr += ` ${key}="${attrs[key]}"`;
		return `<${name}${_attr}>${content}</${name}>`;
	};

	/**
	* Comment create
	* @method comment
	* @param {string} content Comment content
	* @return {string}
	*/
	window.comment = function(content){
		return `<!-- ${content} -->`;
	};

	/**
	* Multiline create
	* @method multiline
	* @param {array} lines Lines
	* @param {boolean} trim Trim content
	* @return {string}
	*/
	window.multiline = function(lines,trim){
		if(trim)
			return lines.join(N).trim();
		else
			return lines.join(N);
	};

	/**
	* Inline create
	* @method inline
	* @param {string} content Content
	* @param {boolean} trim Trim content
	* @return {string}
	*/
	window.inline = function(content,trim){
		if(trim)
			return content.replace(/\n/g, ' ').replace(/\s+/g, ' ').trim();
		else
			return content.replace(/\n/g, ' ').replace(/\s+/g, ' ');
	};

	/**
	* Apply prefix
	* @method applyPrefix
	* @param {string} source Source XML
	* @param {string} prefix Prefix
	* @return {string}
	*/
	window.applyPrefix = function(source,prefix){
		var existPrefixOpen = new RegExp('<'+prefix+':(\\w+):','g');
		var existPrefixClose = new RegExp('</'+prefix+':(\\w+):','g');
		return source.replace(/<(\w)/g,`<${prefix}:$1`)
									.replace(/<\//g,`</${prefix}:`)
									.replace(existPrefixOpen,'<$1:')
									.replace(existPrefixClose,'</$1:');
	};

	/**
	* Delete prefix
	* @method deletePrefix
	* @param {string} source Source XML
	* @param {string} prefix Prefix
	* @return {string}
	*/
	window.deletePrefix = function(source,prefix){
		var prefixOpen = new RegExp('<'+prefix+':','g');
		var prefixClose = new RegExp('</'+prefix+':','g');
		return source.replace(prefixOpen,'<').replace(prefixClose,'</');
	};

}());
