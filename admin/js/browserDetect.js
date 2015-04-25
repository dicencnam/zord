/**
* Global scope
*
* @module GLOBAL
* @main GLOBAL
*/

/**
* Browser detection

	// Browser version
	var browserVersion = {
		Chrome : 24,
		Firefox : 18,
		Explorer : 10,
		Trident : 11,
		Safari : 6,
		Opera : 12.16,
		Android : 34,
		CriOS : 30 // chrome ipad ios7
	};

	// Browser test
	var browserTest = function(browser) {
		if(browserVersion[browser.name]!=undefined){
			if (browser.version >= browserVersion[browser.name]){
				return true;
			}
			alert('Your browser is too old');
			return false;
		}
		alert('Untested browser. The application may not work optimally.');
		return true;
	};

	if(browserTest({name: BrowserDetect.browser,version: BrowserDetect.version})) {
		// continue...
	}

* @class BrowserDetect
* @module GLOBAL
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
(function() {

	var BrowserDetect = {};

	var init = function () {
		BrowserDetect.browser = _browserDetect.searchString(_browserDetect.dataBrowser) || "An unknown browser";
		BrowserDetect.version = _browserDetect.searchVersion(navigator.userAgent)
			|| _browserDetect.searchVersion(navigator.appVersion)
			|| "an unknown version";
	};

	var _browserDetect = {
		searchString: function (data) {
			for (var i=0;i<data.length;i++)	{
				var dataString = data[i].string;
				var dataProp = data[i].prop;
				_browserDetect.versionSearchString = data[i].versionSearch || data[i].identity;
				if (dataString) {
					if (dataString.indexOf(data[i].subString) != -1)
						return data[i].identity;
				}
				else if (dataProp)
					return data[i].identity;
			}
		},

		searchVersion: function (dataString) {
			var index = dataString.indexOf(_browserDetect.versionSearchString);
			if (index == -1) return;
			return parseFloat(dataString.substring(index+_browserDetect.versionSearchString.length+1));
		},

		dataBrowser: [
			{
				string: navigator.userAgent,
				subString: "Chrome",
				identity: "Chrome"
			},
			{ 	string: navigator.userAgent,
				subString: "CriOS",
				versionSearch: "CriOS",
				identity: "CriOS"
			},
			{ 	string: navigator.userAgent,
				subString: "OmniWeb",
				versionSearch: "OmniWeb/",
				identity: "OmniWeb"
			},
			{
				string: navigator.vendor,
				subString: "Apple",
				identity: "Safari",
				versionSearch: "Version"
			},
			{
				prop: window.opera,
				identity: "Opera",
				versionSearch: "Version"
			},
			{
				string: navigator.vendor,
				subString: "iCab",
				identity: "iCab"
			},
			{
				string: navigator.vendor,
				subString: "KDE",
				identity: "Konqueror"
			},
			{
				string: navigator.userAgent,
				subString: "Firefox",
				identity: "Firefox"
			},
			{
				string: navigator.userAgent,
				subString: "Android",
				identity: "Android",
				versionSearch: "AppleWebKit",
			},
			{
				string: navigator.vendor,
				subString: "Camino",
				identity: "Camino"
			},
			{	// for newer Netscapes (6+)
				string: navigator.userAgent,
				subString: "Netscape",
				identity: "Netscape"
			},
			{
				string: navigator.userAgent,
				subString: "MSIE",
				identity: "Explorer",
				versionSearch: "MSIE"
			},
			{
				string: navigator.userAgent,
				subString: "Trident",
				identity: "Trident",
				versionSearch: "rv"
			},
			{
				string: navigator.userAgent,
				subString: "Gecko",
				identity: "Mozilla",
				versionSearch: "rv"
			},
			{	// for older Netscapes (4-)
				string: navigator.userAgent,
				subString: "Mozilla",
				identity: "Netscape",
				versionSearch: "Mozilla"
			}
		]
	};
	init();
	window.BrowserDetect = BrowserDetect;
})();
