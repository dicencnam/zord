<!-- beforebegin -<p>- afterbegin -foo- beforeend -</p>- afterend -->
{{template id="epub" overlay="workspace" insert="replace"}}
<div class="window-title">{{$epub_title}}</div>
<div style="margin:8px;text-align:center;">
	<input id="epub_allselect" type="button" value="{{$allselect}}"/>&#160;&#160;&#160;&#160;&#160;
	<input id="epub_allunselect" type="button" value="{{$allunselect}}"/>
	<span style="display:inline-block;width:405px;"> </span>
	<input id="epub_create" type="button" value="{{$create}}"/>
</div>
<div >
	<div class="panelsTabs" id="epubs_table">
	</div>
</div>
{{/template}}

{{template id="epubs_tab" overlay="epubs_tabs" insert="beforeend" preload="true"}}
<div data-tab="{{$portal}}" class="tab"><div class="frame_title">{{$portal}}</div></div>
{{/template}}

{{template id="epubs_item" lang="false"}}
<tr{{$level}}>
	<td><input type="checkbox" value="{{$isbn}}" data-portal="{{$portal}}"/></td>
	<td>{{$isbn}}</td>
	<td>{{$title}}</td>
	<td>{{$check}}</td>
	<td>{{$download}}</td>
</tr>
{{/template}}

{{template id="epubs_panels" overlay="epubs_panels" insert="beforeend" preload="true"}}
<div data-panel="{{$portal}}" class="panel">
	<table>
		{{$content}}
	</table>
</div>
{{/template}}
