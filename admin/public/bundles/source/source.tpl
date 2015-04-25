<!-- beforebegin -<p>- afterbegin -foo- beforeend -</p>- afterend -->
{{template id="source" overlay="workspace" insert="replace"}}
<div class="window-title">{{$import_title}}</div>
<form id="importTEI">
	<div class="form_service">
		<p><label>{{$websites}} </label>
			<select id="source_websites"></select>
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
		<div id="source_wait" style="margin-top:8px;display:none;">
			<span id="source_msg"></span>
		</div>
		<textarea id="source_report" style="margin:15px auto 0 auto;height: 300px;padding: 10px;overflow: auto;display: none;text-align: left;white-space: pre-wrap;width:90%;" spellcheck="false"></textarea>
		<div id="source_links" style="margin-top:15px;"></div>
	</div>

</form>
{{/template}}

{{template id="source_websites" overlay="source_websites" insert="beforeend" preload="true"}}
<option value="{{$value}}">{{$label}}</option>
{{/template}}

{{template id="source_categories" overlay="source_categories" insert="beforeend" preload="true"}}
<option value="{{$value}}">{{$label}}</option>
{{/template}}
