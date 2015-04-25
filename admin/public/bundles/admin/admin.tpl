<!-- beforebegin -<p>- afterbegin -foo- beforeend -</p>- afterend -->
{{template id="webtoprint_head" overlay="head" insert="beforeend"}}
<cp:stringbundleset id="strbundles" style="display:none;">
</cp:stringbundleset>
{{/template}}

{{template id="bartools" overlay="bartools" insert="inner"}}
<cp:popupboxes id="msg" wait="public/img/wait.gif"></cp:popupboxes>
<span id="admin_portal" class="admin-button">{{$portal}}</span>
<span id="admin_source" class="admin-button">{{$importTEI}}</span>
<span id="admin_publication" class="admin-button">{{$publication}}</span>
<span id="admin_cache" class="admin-button">{{$cache}}</span>
<span id="admin_users" class="admin-button">{{$users}}</span>
<span id="admin_counter" class="admin-button">{{$counter}}</span>
<span id="admin_epub" class="admin-button">{{$epub}}</span>
{{/template}}
