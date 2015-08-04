/**
 * Bundle users
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
define('bdl/users/users',function() {

	var addUser = function(values){
		MSG.wait();
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'addUser',
				users_email: values.users_email,
				users_end: values.users_end,
				users_login: values.users_login,
				users_password: values.users_password,
				users_start: values.users_start,
				websites: values.websites,
				users_level: values.users_level,
				users_type: values.users_type,
				users_name: values.users_name
			},
			success : function (data) {
				MSG.hide();
				[].forEach.call(document.getElementById('users_add').querySelectorAll('input[text]'), function (el,i) {
					el.value = '';
				});
				viewUsers(data.content);
			},
			error : function (code,data) {
				if(303===code)
					MSG.alert('ERROR','<div style="margin-bottom:25px;"><span class="fa fa-warning fa-2x"></span> '+locale.invalid_field+'<br/>'+locale[data.message]+'</div>');
			}
		});
	};

	var updateUser = function(id,email,end,login,start,websites,name){
		MSG.wait();
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'updateUser',
				id: id,
				email: email,
				end: end,
				login: login,
				start: start,
				websites: websites,
				name: name
			},
			success : function (data) {
				MSG.hide();
				viewUsers(data.content);
			},
			error : function (code,data) {
				if(303===code)
					MSG.alert('ERROR','<div style="margin-bottom:25px;"><span class="fa fa-warning fa-2x"></span> '+locale.invalid_field+'<br/>'+locale[data.message]+'</div>');
			}
		});
	};

	var delUser = function(id){
		MSG.wait();
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'delUser',
				id: id
			},
			success : function (data) {
				MSG.hide();
				viewUsers(data.content);
			},
			error : function (code,data) {
				if(303===code)
					MSG.alert('ERROR','<div style="margin-bottom:25px;"><span class="fa fa-warning fa-2x"></span> '+locale.invalid_field+'</div>');
			}
		});
	};

	var getUsers = function(){
		MSG.wait();
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'getUsers'
			},
			success : function (data) {
				MSG.hide();
				viewUsers(data.content);
			}
		});
	};

	var tplWebsiteTable = '<input type="checkbox" name="websites" value="{{$value}}"{{$checked}}/>{{$label}}<br/>';
	var viewWebsites = function(websites){
		var _w = websites.split(',');
		var result = [];;
		WEBSITES.forEach(function(site){
			var checked = '';
			if(_w.indexOf(site)>-1)
				checked = ' checked="checked"';
			result.push($tpl.render(tplWebsiteTable,{value:site,label:site.toUpperCase(),checked:checked}));
		});
		return result.join('');
	};

	var viewUsers = function(users){
		$bundles.tpls.users_item_title();
		for(var user in users){
			users[user].websites = viewWebsites(users[user].websites);
			$bundles.tpls.users_item(users[user]);
		}

		[].forEach.call(document.getElementById('users_list').querySelectorAll('.user_del'), function (el,i) {
			el.addEventListener('click', function(e) {
				MSG.confirm(
					locale.confirm_title,
					locale.confirm_del_user,
					function(save){
						if(save)
							delUser(el.parentNode.parentNode.getAttribute('data-id'));
					}
				);
			});
		});
		[].forEach.call(document.getElementById('users_list').querySelectorAll('.user_update'), function (el,i) {
			el.addEventListener('click', function(e) {
				MSG.confirm(
					locale.confirm_title,
					locale.confirm_update_user,
					function(save){
						if(save){
							var parent = el.parentNode.parentNode;
							var id = parent.getAttribute('data-id');
							var email = parent.querySelector('input[name="email"]').value;
							var end = parent.querySelector('input[name="end"]').value;
							var login = parent.querySelector('input[name="login"]').value;
							var start = parent.querySelector('input[name="start"]').value;
							var name = parent.querySelector('input[name="name"]').value;
							var websites = '';
							[].forEach.call(parent.querySelectorAll('input[name="websites"]:checked'), function (web,i) {
								if(websites=='')
									websites = web.value;
								else
									websites += ','+web.value;
							});
							updateUser(id,email,end,login,start,websites,name);
						}
					}
				);
			});
		});
	};

	var usersView = {
		init : function () {
			locale = $definition.get('i18n!bdl/users/locale/users');
			document.getElementById('user_add_send').addEventListener('click', function(e) {
				var v = $ajax.getFormValues('users_add');
				if(!v.empty){
					MSG.confirm(
						locale.confirm_title,
						locale.confirm_add_user,
						function(save){
							if(save)
								addUser(v.values);
						}
					);
				}
			});
			var websitesEl = document.getElementById('users_websites');
			WEBSITES.forEach(function(site){
				$bundles.tpls.users_websites({value:site,label:site.toUpperCase()});
			});
			getUsers();
		}
	};
	return usersView;
});
