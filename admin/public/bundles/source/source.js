/**
 * Bundle source
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
define('bdl/source/source',function() {

	var locale = null;

	var xsltProcessor = null;

	var portals = [];

	var sendClick = true;

	var teiObj = null;

	var reportEL = null;

	var finalContentReport = [];

	var booksURLS = [];

	var getFinalTest = function(testRNG,name,show,portal,callback){
		var fileName = name.substring(0, name.lastIndexOf("."));
		var save = true;
		// rapport
		var report = [];
		var fileHeadReport = '------------------------------\n'+fileName+' - '+portal+'\n\n';
		if(testRNG!=''){
			report.push(testRNG);
			if(testRNG=='Error check RNG file')
				save = false;
		}
		if(teiObj.structure==null){
			report.push('Undefined struture (titlePage) !!!!! ');
			save = false;
		}
		if(teiObj.imbrications!=undefined && teiObj.imbrications.length>0){
			report.push('Imbrication : ');
			report.push(teiObj.imbrications.join('\n'));
			save = false;
		}
		if(teiObj.dates!=undefined && teiObj.dates.length>0){
			report.push('Dates : ');
			report.push(teiObj.dates.join(', '));
			save = false;
		}
		if(teiObj.category==undefined || teiObj.category == 'undefined'){
			report.push('Category : undefined !');
		}
		if(teiObj.graphics!=undefined && teiObj.graphics.length>0){
			report.push('Graphics not found : ');
			var errorGraphic = [];
			teiObj.graphics.forEach(function(graphic){
				errorGraphic.push('Pos:'+graphic.pos+', Url:'+graphic.url);
			});
			report.push(errorGraphic.join(' ; '));
		}
		if(teiObj.errorRef!=undefined && teiObj.errorRef.length>0){
			report.push('Crossref : ');
			report.push(teiObj.errorRef.join(', '));
		}
		if(teiObj.noHead!=undefined && teiObj.noHead.length>0){
			report.push('No head in div : ');
			report.push(teiObj.noHead.join(', '));
		}
		if(teiObj.pages!=undefined && teiObj.pages.length>0){
			report.push('Pages errors : ');
			report.push(teiObj.pages.join(', '));
		}
		if(teiObj.floatingText>0){
			report.push('floatingText : '+teiObj.floatingText);
		}
		if(teiObj.lbracket.count>0){
			report.push('lbracket : '+teiObj.lbracket.count+"\n"+teiObj.lbracket.txt);
		}
		if(report.length>0){
			if(!save)
				report.unshift('File not saved !!!!!!!!!!!!!!!');
			finalContentReport.push(fileHeadReport+'Errors :-(\n\n'+report.join('\n\n'));
		} else {
			finalContentReport.push(fileHeadReport+'No error  :-)');
		}

		if(save){
			booksURLS.push({name:fileName,url:WEBSITESURL[portal]+'/'+fileName});
			$notify.pub('teiSave:'+portal,[
				portal,
				fileName,
				document.getElementById('source_state').value,
				teiObj.header,
				teiObj.structure,
				teiObj.toc,
				teiObj.abstract,
				teiObj.ids,
				callback
			]);
		} else {
			if(callback!=undefined)
					callback();
		}
		if(show){
			document.getElementById('source_report').innerHTML = finalContentReport.join('\n\n');
			document.getElementById('source_report').style.display = "block";
			document.getElementById('import_tei_send').style.display = 'inline';
			document.getElementById('source_wait').style.display = 'none';
			showURLS();
			sendClick = true;
			MSG.hide();
		}
	};

	var showURLS = function(){
		var linksEl = document.getElementById('source_links');
		linksEl.innerHTML = '';
		booksURLS.forEach(function(link){
			linksEl.insertAdjacentHTML('beforeend',
				'<a href="'+link.url+'" target="_blank">'+link.name+'</a><br/>'
			);
		});
		linksEl.style.display = 'block';
	};

	var getRNG = function(xml,name,show,callback){

		var portalsLength = portals.length;
		portals.forEach(function(portal,i){
			var fileName = name.substring(0, name.lastIndexOf("."));
			$ajax.getJSON({
				url : 'index.php',
				data : {
					module : 'Admin_TEI_'+portal,
					action : 'validation',
					xml : xml,
					repository : portal,
					folder : fileName
				},
				success : function (data) {
					$notify.pub('teiParse:'+portal,[fileName,data.tei,data.graphics,function(_teiObj){
						teiObj = _teiObj;
						var _show = show;
						var _callback = callback;
						if((i+1)<portalsLength){
							_show = false;
							_callback = undefined;
						}
						getFinalTest(data.rng,name,show,portal,callback);
					}]);
				},
				error : function (code,data) {
					MSG.alert('Error',data.message);
				}
			});
		});
	};

	var sourceView = {
		init : function () {
			zip.workerScripts = {
				// deflater: [PATH+'js/z-worker.js', PATH+'js/deflate.js'],
				inflater: [PATH+'js/z-worker.js', PATH+'js/inflate.js']
			};

			var reportEL = document.getElementById('source_report');
			var sendEl = document.getElementById('import_tei_send');
			var waitEl = document.getElementById('source_wait');
			var msgEL = document.getElementById('source_msg');
			// websites
			var websitesEl = document.getElementById('source_websites');
			WEBSITES.forEach(function(site){
				$bundles.tpls.source_websites({value:site,label:site.toUpperCase()});
			});

			sendEl.addEventListener('click', function(e) {
				if(sendClick){
					sendClick = false;
					MSG.wait();
					var file = document.getElementById('fileXML').files[0];
					reportEL.style.display = "none";
					document.getElementById('source_links').style.display = 'none';
					finalContentReport = [];
					booksURLS = [];
					portals = [].slice.call(document.getElementById('source_websites').querySelectorAll('input:checked')).map(
						function(el){
							return el.value
					});
					if(portals.length>0){
						var name = file.name;
						var ext = name.substring(name.lastIndexOf(".")+1, name.length).toLowerCase();
						if (ext=='xml') {
							sendEl.style.display = 'none';
							waitEl.style.display = 'inline';
							var reader = new FileReader();
							reader.onload = function(e) {
								getRNG(this.result,name,true,null);
							};
							reader.readAsText(file);
						} else if (ext=='zip') {
							var errorRapport = [];

							zip.createReader(new zip.BlobReader(file), function(reader) {
								// get all entries from the zip
								reader.getEntries(function(entries) {
									if (entries.length) {
										var entriesLength = entries.length;

										var splitFnc = function(entrieNum){
											var name = entries[entrieNum].filename;
											var fileName = name.substring(0, name.lastIndexOf("."));
											msgEL.innerHTML = fileName+'<br/>'+(entrieNum+1)+'/'+entriesLength;

											// get first entry content as text
											entries[entrieNum].getData(new zip.TextWriter(), function(xml) {
												var callBack = undefined;
												if(entrieNum<entriesLength-1){
													var callback = function(){
														splitFnc(entrieNum+1);
													};
												} else {
													reader.close();
												}
												if(callback!=undefined)
													getRNG(xml,name,false,callback);
												else
													getRNG(xml,name,true,callback);
											});
										};
										sendEl.style.display = 'none';
										waitEl.style.display = 'inline';
										splitFnc(0);
									}
								});
							});
						} else {
							MSG.alert('Error','File not allowed');
						}
					} else {
						MSG.alert('Error','No portal');
					}
				}
			});
		}
	};
	return sourceView;
});
