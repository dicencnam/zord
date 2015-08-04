<!-- beforebegin -<p>- afterbegin -foo- beforeend -</p>- afterend -->
{{template id="counter" overlay="workspace" insert="replace"}}
<div class="window-title">{{$counter_title}}</div>
<div id="counter_form" class="form_service">
	<p><label>{{$users}} </label><select id="counter_users"></select></p>
	<p><label>{{$from}}* </label><input type="month" id="counter_start" data-empty="no"/></p>
	<p><label>{{$to}}* </label><input type="month" id="counter_end" data-empty="no"/></p>
	<p>* YYYY-MM</p>
	<div style="text-align:center;margin-top:15px;">
		<input id="counter_send" type="button" value="{{$report}}"/>
	</div>
</div>


<div id="counter_table_report2" class="counter_table">
</div>
<div id="counter_create_file_report2" class="counter_create_file">
	<a id="counter_send_file_report2">{{$load_report2}}</a>
</div>
<div id="counter_table_report5" class="counter_table">
</div>
<div id="counter_create_file_report5" class="counter_create_file">
	<a id="counter_send_file_report5">{{$load_report5}}</a>
</div>
{{/template}}

{{template id="counter_users" overlay="counter_users" insert="beforeend" preload="true"}}
<option value="{{$id}}">{{$name}}</option>
{{/template}}
