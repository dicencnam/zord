<!-- beforebegin -<p>- afterbegin -foo- beforeend -</p>- afterend -->
{{template id="cache" overlay="workspace" insert="replace"}}
<div class="window-title">{{$cache_title}}</div>
<div style="text-align:center;">
<input id="cache_send" type="button" value="{{$empty}}"/>
</div>
{{/template}}

{{template id="cache_confirm_title" preload="true"}}{{$confirm_title}}{{/template}}

{{template id="cache_confirm_content" preload="true"}}
<h3>{{$confirm_content}}</h3>
{{/template}}
