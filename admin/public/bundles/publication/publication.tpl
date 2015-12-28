<!-- beforebegin -<p>- afterbegin -foo- beforeend -</p>- afterend -->
{{template id="publication" overlay="workspace" insert="replace"}}
<div class="window-title">{{$publication_title}}</div>
<div style="margin-top:8px;margin-bottom:18px;text-align:center;">
	<input id="publication_email_news" type="button" value="{{$email_news}}"/>
</div>
<div style="margin:8px;text-align:center;">
	<div>
		<span id="publication_websites"></span><input id="delete_allselect" type="button" value="{{$allselect}}"/>
	</div>
	<input id="delete_allunselect" type="button" value="{{$allunselect}}"/>
	<span style="display:inline-block;width:700px;"> </span>
	<input id="delete_del" type="button" value="{{$del}}"/>
</div>
<div>
	<div class="panelsTabs" id="publication_table">
		<div class="tabs" id="publication_tabs"></div>
		<div class="panels" id="publication_panels"></div>
	</div>
</div>
{{/template}}

{{template id="publication_item" lang="false"}}
<tr{{$level}} data-id="{{$isbn}}" data-repository="{{$portal}}">
	<td><input type="checkbox" value="{{$isbn}}" data-type="del" class="pub-selector"/></td>
	<td><a class="pub-href" href="{{$load}}" target="_blank">{{$isbn}}</a></td>
	<td><a class="pub-href" href="{{$href}}" target="_blank">{{$title}}</a></td>
	<td>{{$date}}</td>
	<td>
		<select class="pub-draft">
			<option value="0">{{$issued}}</option>
			<option value="1" {{$selected_draft}}>{{$draft}}</option>
		</select>
	</td>
	<td style="text-align:center;"><input type="checkbox" value="{{$isbn}}" data-type="novelty" {{$checked_novelty}}/></td>
	<td style="text-align:center;"><a class="pub-href" href="{{$download}}">↥</a></td>
</tr>
{{/template}}

{{template id="publication_panels" overlay="publication_panels" insert="beforeend" preload="true"}}
<div data-panel="{{$portal}}" class="panel">
	<table>
	<tr>
		<th></th>
		<th>{{$label_isbn}}</th>
		<th>{{$label_title}}</th>
		<th>{{$label_date}}</th>
		<th>{{$label_status}}</th>
		<th>{{$label_novelty}}</th>
		<th>{{$label_download}}</th>
	</tr>
	{{$content}}
	</table>
</div>
{{/template}}

{{template id="publication_websites" overlay="publication_websites" insert="beforeend" preload="true"}}
<label><input type="checkbox" name="websites" value="{{$value}}" checked="checked"/>{{$label}}</label>
{{/template}}

{{template id="publication_tab" overlay="publication_tabs" insert="beforeend" preload="true"}}
<div data-tab="{{$portal}}" class="tab"><div class="frame_title">{{$portal}}</div></div>
{{/template}}
