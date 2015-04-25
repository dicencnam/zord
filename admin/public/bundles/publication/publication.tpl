<!-- beforebegin -<p>- afterbegin -foo- beforeend -</p>- afterend -->
{{template id="publication" overlay="workspace" insert="replace"}}
<div class="window-title">{{$publication_title}}</div>
<div style="margin:8px;text-align:center;">
	<input id="delete_allselect" type="button" value="{{$allselect}}"/>&#160;&#160;&#160;&#160;&#160;
	<input id="delete_allunselect" type="button" value="{{$allunselect}}"/>
	<span style="display:inline-block;width:405px;"> </span>
	<input id="delete_del" type="button" value="{{$del}}"/>
</div>
<div>
	<table id="publication_table"></table>
</div>
{{/template}}

{{template id="publication_item" overlay="publication_table" insert="beforeend" preload="true"}}
<tr{{$level}} data-id="{{$isbn}}" data-repository="{{$portal}}">
	<td><input type="checkbox" value="{{$isbn}}" data-type="del" class="pub-selector"/></td>
	<td>{{$isbn}}</td>
	<td>{{$title}}</td>
	<td>
		<select class="pub-draft">
			<option value="0">{{$issued}}</option>
			<option value="1" {{$selected_draft}}>{{$draft}}</option>
		</select>
	</td>
	<td style="text-align:center;"><input type="checkbox" value="{{$isbn}}" data-type="novelty" {{$checked_novelty}}/></td>
	<td style="text-align:center;"><a class="pub-download" href="{{$downlod}}">↥</a></td>
</tr>
{{/template}}

{{template id="publication_portal" overlay="publication_table" insert="beforeend" preload="true"}}
<tr>
	<th colspan="6" style="font-size:1.3em;text-transform: uppercase;">{{$portal}}</th>
</tr>
<tr>
	<th></th>
	<th>{{$label_isbn}}</th>
	<th>{{$label_title}}</th>
	<th>{{$label_status}}</th>
	<th>{{$label_novelty}}</th>
	<th>{{$label_download}}</th>
</tr>
{{/template}}
