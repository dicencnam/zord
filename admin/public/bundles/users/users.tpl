<!-- beforebegin -<p>- afterbegin -foo- beforeend -</p>- afterend -->
{{template id="users" overlay="workspace" insert="replace"}}
<div class="window-title">{{$users_title}}</div>
<div style="text-align:center;">
	<form id="users_add">
		<div class="form_service">
			<h3>{{$add_title}}</h3>
			<p><label>{{$type_label}} </label>
				<select id="users_type">
					<option value="USER">{{$user_label}}</option>
					<option value="IP">{{$ip_label}}</option>
				</select>
			</p>

			<p><label>{{$name_label}} </label><input type="text" id="users_name" data-empty="no" /></p>

			<p><label>{{$login_label}} </label><input type="text" id="users_login" data-empty="no" /></p>

			<p id="users_ip" style="display:none;"><label>{{$ip_label}} </label><input type="text" id="users_ip" data-empty="no" /></p>

			<p><label>{{$password_label}}* </label><input type="password" id="users_password" data-empty="no" /></p>

			<p><label>{{$email_label}} </label><input type="email" id="users_email" data-empty="no" /></p>
			<p><label>{{$start_label}}** </label><input type="date" id="users_start" data-empty="no" /></p>
			<p><label>{{$end_label}}** </label><input type="date" id="users_end" data-empty="no" /></p>
			<p><label>{{$subscription_label}}*** </label><input type="number" step="10" value="0" min="0" data-empty="no" id="users_subscription"/></p>
			<p><label>{{$website_label}} </label><div id="users_websites"></div></p>

			<p><label>{{$level}} </label>
				<select id="users_level">
					<option value="0">{{$user}}</option>
					<option value="1">{{$admin}}</option>
				</select>
			</p>

			<p style="font-size:0.8em;text-align:left;padding-left:200px;">
				{{$password_indic}}<br/>
				{{$date_indic}}<br/>
				{{$subscription_indic}}
			</p>
			<br/>
			<input id="user_add_send" type="button" value="{{$add}}"/>
		</div>
	</form>
	<div>
		<h3>{{$users_list_title}}</h3>
		<table border="1" id="users_list">

		</table>
	</div>
	<br/><br/><br/><br/>
</div>
{{/template}}

{{template id="users_item_title" overlay="users_list" insert="replace" preload="true"}}
<tr>
	<th>{{$name_label}}</th>
	<th>{{$login_label}}</th>
	<th>{{$ip_label}}</th>
	<th>{{$email_label}}</th>
	<th>{{$start_label}}</th>
	<th>{{$end_label}}</th>
	<th>{{$subscription_label}}</th>
	<th>{{$website_label}}</th>
	<th>{{$type_label}}</th>
	<th>{{$del_label}}</th>
	<th>{{$update_label}}</th>
</tr>
{{/template}}

{{template id="users_item" overlay="users_list" insert="beforeend" preload="true"}}
<tr data-id="{{$id}}" class="users_level{{$level}}">
	<td><input type="text" name="name" value="{{$name}}" /></td>
	<td><input type="text" name="login" value="{{$login}}" /></td>
	<td><input type="text" name="ip" value="{{$ip}}" disabled="{{$ipcl}}"/></td>
	<td><input type="text" name="email" value="{{$email}}" /></td>
	<td><input type="date" name="start" value="{{$start}}" /></td>
	<td><input type="date" name="end" value="{{$end}}" /></td>
	<td><input type="number" name="subscription" value="{{$subscription}}" step="10" min="0" /></td>
	<td style="text-align:left;">{{$websites}}</td>
	<td>{{$type}}</td>
	<td><span class="user_del">✖</span></td>
	<td><span class="user_update">✔</span></td>
</tr>
{{/template}}

{{template id="users_websites" overlay="users_websites" insert="beforeend" preload="true"}}
<input type="checkbox" name="websites" value="{{$value}}" checked="checked"/>{{$label}}<br/>
{{/template}}
