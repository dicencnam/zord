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
	<table id="epubs_table">

	</table>
</div>
{{/template}}

{{template id="epub_item" overlay="epubs_table" insert="beforeend" preload="true"}}
<tr{{$level}}>
	<td><input type="checkbox" value="{{$isbn}}" data-portal="{{$portal}}"/></td>
	<td>{{$isbn}}</td>
	<td>{{$title}}</td>
</tr>
{{/template}}

{{template id="epub_portal" overlay="epubs_table" insert="beforeend" preload="true"}}
<tr>
	<th colspan="3">{{$portal}}</th>
</tr>
{{/template}}
