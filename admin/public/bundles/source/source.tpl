<!-- beforebegin -<p>- afterbegin -foo- beforeend -</p>- afterend -->
{{template id="source" overlay="workspace" insert="replace"}}
<div class="window-title">{{$import_title}}</div>
<form id="importTEI">
	<div class="form_service">
		<p><label>{{$websites}} </label>
			<div id="source_websites"></div>
		</p>
		<p><label>{{$status}} </label>
			<select id="source_state">
				<option value="1">{{$draft}}</option>
				<option value="0">{{$issued}}</option>
			</select>
		</p>
		<p><label>{{$file}} </label>
			<input type="file" id="fileXML" data-empty="no" data-extension="xml"/>
		</p>
		<br/>
		<input id="import_tei_send" type="button" value="{{$send}}"/>
		<div id="source_wait">
			<span id="source_msg"></span>
		</div>
		<textarea id="source_report" spellcheck="false"></textarea>
		<div id="source_links"></div>
	</div>

</form>
{{/template}}

{{template id="source_websites" overlay="source_websites" insert="beforeend" preload="true"}}
<label><input type="checkbox" name="websites" value="{{$value}}"/>{{$label}}</label>
{{/template}}

{{template id="source_categories" overlay="source_categories" insert="beforeend" preload="true"}}
<option value="{{$value}}">{{$label}}</option>
{{/template}}
