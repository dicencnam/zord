
document.addEventListener("DOMContentLoaded", function(event) {

	var noveltyEl = document.getElementById('noveltyHtml');
	var docsEl = document.getElementById('docsHtml');
	new Tablesort(noveltyEl);
	new Tablesort(docsEl);

	document.getElementById('allselect').addEventListener('click', function(event){
		[].forEach.call(noveltyEl.querySelectorAll('input[data-type="check"]'), function (el) {
			el.checked = true;
		});
		[].forEach.call(docsEl.querySelectorAll('input[data-type="check"]'), function (el) {
			el.checked = true;
		});
	});
	document.getElementById('allunselect').addEventListener('click', function(event){
		[].forEach.call(noveltyEl.querySelectorAll('input[data-type="check"]'), function (el) {
			el.checked = false;
		});
		[].forEach.call(docsEl.querySelectorAll('input[data-type="check"]'), function (el) {
			el.checked = false;
		});
	});

	var create = function(format){
		var ids = [];
		[].forEach.call(noveltyEl.querySelectorAll('input[data-type="check"]'), function (el) {
			if(el.checked)
				ids.push(el.value);
		});
		[].forEach.call(docsEl.querySelectorAll('input[data-type="check"]'), function (el) {
			if(el.checked)
				ids.push(el.value);
		});
		if(ids.length>0){
			$getJSON({
				url : 'index.php',
				data : {
					module : 'Search',
					action : 'noticesSetIDS',
					format : format,
					ids : JSON.stringify(ids)
				},
				success : function(data){
					document.body.insertAdjacentHTML('beforeend', "<iframe src='"+PATH+'Motor/getNotices'+
						"' style='display: none;' ></iframe>");
				}
			});
		}
	};

	document.getElementById('create_marcxml').addEventListener('click', function(event){
		create('marcxml');
	});
	document.getElementById('create_mods').addEventListener('click', function(event){
		create('mods');
	});

});
