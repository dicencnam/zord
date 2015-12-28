/**
 * Bundle portal
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
define('bdl/portal/portal',function() {

	var portalView = {
		init : function () {
			var locale = $definition.get('i18n!bdl/portal/locale/portal');
			document.getElementById('addPortal_send').addEventListener('click', function(event){
				var portal = document.getElementById('addPortal_name').value;
				var portalname = document.getElementById('addPortal_namelong').value;
				var url = document.getElementById('addPortal_url').value;
				var publisher = document.getElementById('addPortal_publisher').value;
				if(portal!='' && url!='' && publisher!='' && portalname!=''){
					MSG.wait();
					$ajax.getJSON({
						url : 'index.php',
						data : {
							module : 'Admin_action',
							action : 'portalNew',
							portal : portal,
							url : url,
							portalname:portalname,
							publisher : publisher
						},
						success : function (data) {
							WEBSITES.push(portal);
							WEBSITESURL[portal] = url;
							document.getElementById('addPortal_name').value = '';
							document.getElementById('addPortal_url').value = '';
							document.getElementById('addPortal_publisher').value = '';
							$ajax.getJSON({
								url : 'index.php',
								data : {
									module : 'Admin_action',
									action : 'emptyCache'
								},
								success : function (data) {
									MSG.hide();
								}
							});
						},
						error : function (code,data) {
							if(303===code)
								MSG.alert('ERROR','<div style="margin-bottom:25px;"><span class="fa fa-warning fa-2x"></span> '+locale.invalid_field+'</div>');
						}
					});
				}
			});

			document.getElementById('delPortal_send').addEventListener('click', function(event){
				var portal = document.getElementById('delPortal_name').value;
				var url = document.getElementById('delPortal_url').value;
				if(portal!='' && url!=''){
					MSG.wait();
					$ajax.getJSON({
						url : 'index.php',
						data : {
							module : 'Admin_action',
							action : 'portalDel',
							portal : portal,
							url : url
						},
						success : function (data) {
							WEBSITES.splice(WEBSITES.indexOf(portal), 1);
							delete WEBSITESURL[portal];
							document.getElementById('delPortal_name').value = '';
							document.getElementById('delPortal_url').value = '';
							$ajax.getJSON({
								url : 'index.php',
								data : {
									module : 'Admin_action',
									action : 'emptyCache'
								},
								success : function (data) {
									MSG.hide();
								}
							});
						},
						error : function (code,data) {
							if(303===code)
								MSG.alert('ERROR','<div style="margin-bottom:25px;"><span class="fa fa-warning fa-2x"></span> '+locale.invalid_field+'</div>');
						}
					});
				}
			});
		}
	};
	return portalView;
});
