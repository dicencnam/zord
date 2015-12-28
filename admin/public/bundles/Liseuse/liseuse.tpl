<!-- beforebegin -<p>- afterbegin -foo- beforeend -</p>- afterend -->
{{template id="Liseuse" overlay="body" insert="inner"}}

<cp:popupboxes id="msg" wait="images/g_wait.gif" cancel="images/g_cancel.png" ok="images/g_ok.png"></cp:popupboxes>
<cp:epubopf id="epubopf"></cp:epubopf>
<div id="book_doc_spine" style="display:none;"></div>
<div id="book_doc_ncx" ></div>
<div id="epubliseuse_div">
<cp:epubliseuse id="epubliseuse" style="margin:auto;" lang="fr" width="700" height="500"></cp:epubliseuse>
</div>
{{/template}}
