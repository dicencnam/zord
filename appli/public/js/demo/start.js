/**
* demo start
*/
(function(undefined) {
	document.addEventListener("DOMContentLoaded", function(event) {
		var element = document.getElementById('start_source');
		$panelsTabs(element);
		[].forEach.call(element.querySelectorAll('table'), function (el) {
			new Tablesort(el);
		});
	});
})();
