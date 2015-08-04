<!-- beforebegin -<p>- afterbegin -foo- beforeend -</p>- afterend -->
{{template id="portal" overlay="workspace" insert="replace"}}
<div class="window-title">{{$portal_title}}</div>
<div style="text-align:center;">

</div>
<form id="addPortal">
	<div class="form_service">
		<h3>{{$addPortal}}</h3>
		<p>
			<label>{{$Portal_name}} </label>
			<input type="text" id="addPortal_name" placeholder="portal"/>
		</p>
		<p>
			<label>{{$Portal_url}} </label>
			<input type="text" id="addPortal_url"  placeholder="http://portal.domain.com"/>
		</p>
		<p>
			<label>{{$Portal_publisher}} </label>
			<input type="text" id="addPortal_publisher"  placeholder="Publisher"/>
		</p>
		<br/>
		<input id="addPortal_send" type="button" value="{{$addPortal_send}}"/>
	</div>
</form>

<form id="delPortal">
	<div class="form_service">
		<h3>{{$delPortal}}</h3>
		<p>
			<label>{{$Portal_name}} </label>
			<input type="text" id="delPortal_name" placeholder="portal"/>
		</p>
		<p>
			<label>{{$Portal_url}} </label>
			<input type="text" id="delPortal_url"  placeholder="http://portal.domain.com"/>
		</p>
		<br/>
		<input id="delPortal_send" type="button" value="{{$delPortal_send}}"/>
	</div>
</form>
{{/template}}
