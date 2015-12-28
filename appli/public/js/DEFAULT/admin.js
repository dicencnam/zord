/**
* __PORTALDEFAULT__ admin
*/
(function(undefined) {

	var SELECTED_SUBS = 0;
	var SIZE_SUBS = 0;
	var BOOKS_SUBS = null;

	var setCounter = function(element){
		document.getElementById('subscription_counter').innerHTML = SELECTED_SUBS;
		BOOKS_SUBS.forEach(function(isbn){
			[].forEach.call(element.querySelectorAll('input[value="'+isbn+'"]'), function (el) {
				if(!el.checked)
					el.setAttribute('checked','checked');
			});
		});
	};


	var initSelectBook = function(element){
		[].forEach.call(element.querySelectorAll('input[data-type="check"]'), function (el) {
			el.addEventListener("change", function() {
				var isbn = this.value;
				if(this.checked){
					if(SELECTED_SUBS<SIZE_SUBS){
						BOOKS_SUBS.push(isbn);
						SELECTED_SUBS = BOOKS_SUBS.length;
					} else {
						this.removeAttribute('checked');
						this.checked = false;
					}
				} else {
					var index = BOOKS_SUBS.indexOf(isbn);
					BOOKS_SUBS.splice(index,1);
					SELECTED_SUBS = BOOKS_SUBS.length;
				}
				setCounter(element);
			});
		});
	};

	var getSubscription = function(){
		$dialog.wait();
		$getJSON({
			url : 'index.php',
			data : {
				module : 'AdminUser',
				action : 'getSubscription'
			},
			success : function(data){
				var docs = document.getElementById('admin_content');
				docs.innerHTML = data.content;
				BOOKS_SUBS = data.books;
				SIZE_SUBS = data.subscription;
				SELECTED_SUBS = BOOKS_SUBS.length;
				setCounter(docs);
				initSelectBook(docs);

				$panelsTabs(docs);
				document.getElementById('subscription_save').addEventListener("click", function() {
					setSubscription();
				});

				[].forEach.call(docs.querySelectorAll('table'), function (el) {
					new Tablesort(el);
				});
				$dialog.hideDelay();
			},
			error : function (code,data) {
				if(303===code)
					$dialog.box('<div style="padding:25px;background:rgb(223, 223, 223);"><span class="fa fa-warning fa-2x"></span> Unauthorized</div>');
			}
		});
	};

	var setSubscription = function(){
		$dialog.wait();
		$getJSON({
			url : 'index.php',
			data : {
				module : 'AdminUser',
				action : 'setSubscription',
				books : JSON.stringify(BOOKS_SUBS)
			},
			success : function(){
				$dialog.hideDelay();
			},
			error : function (code,data) {
				if(303===code)
					$dialog.box('<div style="padding:25px;background:rgb(223, 223, 223);"><span class="fa fa-warning fa-2x"></span> Unauthorized</div>');
			}
		});
	};

	document.addEventListener("DOMContentLoaded", function(event) {
		document.getElementById('admin_logout').addEventListener("click", function() {
			console.log(document.getElementById('userconnex'));
			document.getElementById('userconnex').submit();
		});
		if(SUBSCRIPTION){
			document.getElementById('admin_select_titles').style.display = 'inline-block';
			document.getElementById('admin_select_titles').addEventListener("click", function() {
				getSubscription();
			});
		}
	});

})();
