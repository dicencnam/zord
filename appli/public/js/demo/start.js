/**
* demo start
*/
(function(undefined) {
	document.addEventListener("DOMContentLoaded", function(event) {

		var tables = ['source','nosource','biblio'];
		var showFrame = function(name){
			tables.forEach(function(n){
				var elT = document.getElementById('table_'+n);
				var elF = document.getElementById('frame_'+n);
				if(name==n){
					elT.style.display = 'table';
					if(!elF.classList.contains('paneselect'))
						elF.classList.add('paneselect');
				} else {
					elT.style.display = 'none';
					if(elF.classList.contains('paneselect'))
						elF.classList.remove('paneselect');
				}
			});
		};
		tables.forEach(function(name){
			document.getElementById('frame_'+name).addEventListener("click", function(event) {
				showFrame(name);
			});
			new Tablesort(document.getElementById('table_'+name));
		});
	});
})();
