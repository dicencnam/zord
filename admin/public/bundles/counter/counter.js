/**
 * Bundle counter
 * @author David Dauvergne
 * @copyright 2014 David Dauvergne
 * @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */
define('bdl/counter/counter',function() {
	var users = null;
	var books = null;

	var getUsers = function(callback){
		MSG.wait();
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'getCounterDatas'
			},
			success : function (data) {
				MSG.hide();
				users = data.users;
				books = {};
				for(var portal in data.books){
					for(var isbn in data.books[portal])
					books[isbn] = data.books[portal][isbn].title;
				}
				callback();
			}
		});
	};
	var counter_start = null;
	var counter_end = null;
	var counter_users = null;
	var getTodayDate = function(){
		var now = new Date();
		var day = ("0" + now.getDate()).slice(-2);
		var month = ("0" + (now.getMonth() + 1)).slice(-2);
		return now.getFullYear() + "-" + (month) + "-" + (day);
	};
	var getCounterUserRapport_2 = function(validation){
		MSG.wait();
		counter_start = validation.values.counter_start;
		counter_end = validation.values.counter_end;
		counter_users = validation.values.counter_users;
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'getCounterUserRapport_2',
				user : validation.values.counter_users,
				start : validation.values.counter_start,
				end : validation.values.counter_end,
			},
			success : function (data) {

				var emptydates = '';
				for (i = 0; i <= data.dates.length+2; i++) {
					emptydates += '<th></th>';
				}
				emptydates += '</tr>';

				var table = ['<tr>'];

				table.push('<tr><th width="700">Book Report 2 (R4) -  Number of Successful Section Requests by Month and Title</th>'+emptydates);
				userSelect = null;
				for(var _user in users){
					if(users[_user].id==counter_users){
						userSelect = users[_user];
						break;
					}
				}
				table.push('<tr><th width="700">The Section Type: Chapter, index.</th>'+emptydates);
				table.push('<tr><th width="700">'+userSelect.name+'</th>'+emptydates);

				table.push('<tr><th width="700">Period covered by the report: '+data.dates[0]+' to '+data.dates[data.dates.length-1]+'</th>'+emptydates);

				table.push('<tr><th width="700">Date run: '+getTodayDate()+'</th>'+emptydates);

				table.push('<tr><th width="700"></th>');
				table.push('<th>Portals</th>');
				table.push('<th>ISBN</th>');
				for(var date in data.dates){
					table.push('<th>'+data.dates[date]+'</th>');
				}
				table.push('<th>Total</th>');
				table.push('</tr>');

				table.push('<tr><th width="700">Total for all titles</th><th>'+data.portals.join(',')+'</th><th></th>');
				for(var date in data.datesTT){
					table.push('<th>'+data.datesTT[date]+'</th>');
				}
				table.push('<th>'+data.total+'</th></tr>');
				for(var isbn in data.documents){
					table.push('<tr>'); // bgcolor="#CCC"
					table.push('<td width="700">'+books[isbn]+'</td>');
					table.push('<td>'+data.documents[isbn]['portal'].join(',')+'</td>');
					table.push('<td>'+isbn+'</td>');
					var dates = data.documents[isbn]['dates'];
					for(var date in dates){
						table.push('<td>'+dates[date]+'</td>');
					}
					table.push('<td>'+data.documents[isbn]['tt']+'</td>');
					table.push('</tr>');
				}

				var content = '<table>'+table.join('')+'</table>';
				document.getElementById('counter_table_report2').innerHTML = content;
				window.URL = window.URL || window.webkitURL;

				var css = '<style type="text/css">table {width:1500px;} td {width:700px;}</style>';
				var head = '<html><head><meta http-equiv=Content-Type content="text/html; charset=utf-8">'+css+'</head><body>';
				var blob = new Blob([head+content+'</body></html>'], {type: 'application/vnd.ms-excel'});

				document.getElementById('counter_send_file_report2').setAttribute("href", window.URL.createObjectURL(blob));
				document.getElementById('counter_send_file_report2').setAttribute("download", 'raport_'+counter_users+'_'+counter_start+'_'+counter_end+'.xls');
				document.getElementById('counter_table_report2').style.display = 'block';
				document.getElementById('counter_create_file_report2').style.display = 'block';
				MSG.hide();
			}
		});
	};
	var getCounterUserRapport_5 = function(validation){
		MSG.wait();
		counter_start = validation.values.counter_start;
		counter_end = validation.values.counter_end;
		counter_users = validation.values.counter_users;
		$ajax.getJSON({
			url : 'index.php',
			data : {
				module : 'Admin_action',
				action : 'getCounterUserRapport_5',
				user : validation.values.counter_users,
				start : validation.values.counter_start,
				end : validation.values.counter_end,
			},
			success : function (data) {

				var emptydates = '';
				for (i = 0; i <= data.dates.length+2; i++) {
					emptydates += '<th></th>';
				}
				emptydates += '</tr>';

				var table = ['<tr>'];

				table.push('<tr><th width="700">Book Report 5 (R4) -  Total Searches by Month and Title</th>'+emptydates);
				userSelect = null;
				for(var _user in users){
					if(users[_user].id==counter_users){
						userSelect = users[_user];
						break;
					}
				}

				table.push('<tr><th width="700">'+userSelect.name+'</th>'+emptydates);

				table.push('<tr><th width="700">Period covered by the report: '+data.dates[0]+' to '+data.dates[data.dates.length-1]+'</th>'+emptydates);

				table.push('<tr><th width="700">Date run: '+getTodayDate()+'</th>'+emptydates);

				table.push('<tr><th width="700"></th>');
				table.push('<th>Portals</th>');
				table.push('<th>ISBN</th>');
				for(var date in data.dates){
					table.push('<th>'+data.dates[date]+'</th>');
				}
				table.push('<th>Total</th>');
				table.push('</tr>');

				table.push('<tr><th width="700">Total for all titles</th><th>'+data.portals.join(',')+'</th><th></th>');
				for(var date in data.datesTT){
					table.push('<th>'+data.datesTT[date]+'</th>');
				}
				table.push('<th>'+data.total+'</th></tr>');
				for(var isbn in data.documents){
					table.push('<tr>'); // bgcolor="#CCC"
					table.push('<td width="700">'+books[isbn]+'</td>');
					table.push('<td>'+data.documents[isbn]['portal'].join(',')+'</td>');
					table.push('<td>'+isbn+'</td>');
					var dates = data.documents[isbn]['dates'];
					for(var date in dates){
						table.push('<td>'+dates[date]+'</td>');
					}
					table.push('<td>'+data.documents[isbn]['tt']+'</td>');
					table.push('</tr>');
				}

				var content = '<table>'+table.join('')+'</table>';
				document.getElementById('counter_table_report5').innerHTML = content;
				window.URL = window.URL || window.webkitURL;

				var css = '<style type="text/css">table {width:1500px;} td {width:700px;}</style>';
				var head = '<html><head><meta http-equiv=Content-Type content="text/html; charset=utf-8">'+css+'</head><body>';
				var blob = new Blob([head+content+'</body></html>'], {type: 'application/vnd.ms-excel'});

				document.getElementById('counter_send_file_report5').setAttribute("href", window.URL.createObjectURL(blob));
				document.getElementById('counter_send_file_report5').setAttribute("download", 'raport_'+counter_users+'_'+counter_start+'_'+counter_end+'.xls');
				document.getElementById('counter_table_report5').style.display = 'block';
				document.getElementById('counter_create_file_report5').style.display = 'block';
				MSG.hide();
			}
		});
	};
	var counterView = {
		init : function () {
			getUsers(function(){
				for(var user in users)
					$bundles.tpls.counter_users(users[user]);
			});
			document.getElementById('counter_send').addEventListener('click', function(event){
				var validation = $ajax.getFormValues('counter_form');
				document.getElementById('counter_table_report5').style.display = 'none';
				document.getElementById('counter_create_file_report5').style.display = 'none';
				document.getElementById('counter_table_report2').style.display = 'none';
				document.getElementById('counter_create_file_report2').style.display = 'none';
				getCounterUserRapport_2(validation);
				getCounterUserRapport_5(validation);
			});
		}
	};
	return counterView;
});
