<?php
echo <<<EOD

<div id="start_novelty">

<h1>{$v['lang']->new}</h1>
<table id="table_novelty">
	<thead>
		<tr>
		<th style="width: 30px;">{$v['lang']->date}</th>
		<th style="width: 30px;">{$v['lang']->date}</th>
		<th style="width: 150px;">{$v['lang']->authors}</th>
		<th style="width: 450px;">{$v['lang']->title}</th>
		<th style="width: 200px;">{$v['lang']->editors}</th>
		<th style="width: 55px;">{$v['lang']->publication_date}</th>
		</tr>
	</thead>
	<tbody>{$v['books']['novelty']}</tbody>
</table>

</div>
<div id="start_source">
<div id="frame_source" class="framer paneselect"><div class="frame_title">{$v['lang']->source}</div></div>
<div id="frame_nosource" class="framer"><div class="frame_title">{$v['lang']->nosource}</div></div>
<div id="frame_biblio" class="framer"><div class="frame_title">{$v['lang']->bibliography}</div></div>
</div>
<div id="start_tables">
<table id="table_source">
	<thead>
		<tr>
			<th style="width: 30px;">{$v['lang']->date}</th>
			<th style="width: 30px;">{$v['lang']->date}</th>
			<th style="width: 150px;">{$v['lang']->authors}</th>
			<th style="width: 450px;">{$v['lang']->title}</th>
			<th style="width: 200px;">{$v['lang']->editors}</th>
			<th style="width: 55px;">{$v['lang']->publication_date}</th>
		</tr>
	</thead>
	<tbody>{$v['books']['source']}</tbody>
</table>
<table id="table_nosource">
	<thead>
		<tr>
			<th style="width: 150px;">{$v['lang']->authors}</th>
			<th style="width: 500px;">{$v['lang']->title}</th>
			<th style="width: 200px;">{$v['lang']->editors}</th>
			<th style="width: 55px;">{$v['lang']->publication_date}</th>
		</tr>
	</thead>
	<tbody>{$v['books']['nosource']}</tbody>
</table>
<table id="table_biblio">
	<thead>
		<tr>
			<th style="width: 30px;">{$v['lang']->date}</th>
			<th style="width: 30px;">{$v['lang']->date}</th>
			<th style="width: 150px;">{$v['lang']->authors}</th>
			<th style="width: 450px;">{$v['lang']->title}</th>
			<th style="width: 200px;">{$v['lang']->editors}</th>
			<th style="width: 55px;">{$v['lang']->publication_date}</th>
		</tr>
	</thead>
	<tbody>{$v['books']['biblio']}</tbody>
</table>
</div>
EOD;
?>
