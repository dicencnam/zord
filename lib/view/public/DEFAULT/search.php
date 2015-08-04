<script language="Javascript">
	var LOCALES = {
		noresult : '<?php echo $v['lang']->noresult; ?>',
		occBefore : '<?php echo $v['lang']->occBefore; ?>',
		occAfter : '<?php echo $v['lang']->occAfter; ?>',
		occBefore_p : '<?php echo $v['lang']->occBefore_p; ?>',
		occAfter_p : '<?php echo $v['lang']->occAfter_p; ?>',
		book : '<?php echo $v['lang']->book; ?>',
		book_p : '<?php echo $v['lang']->book_p; ?>',
		occMsg : '<?php echo $v['lang']->occMsg; ?>',
		occMsg_p : '<?php echo $v['lang']->occMsg_p; ?>',
	};
</script>
<div id="search" role="search">
		<form id="form_search">
			<div id="searchBlock">
				<button id="searchButton" title="<?php echo $v['lang']->search; ?>">
					<img src="<?php echo BASEURL ?>public/img/__PORTALDEFAULT__/search.png" />
				</button><input id="query" type="search" placeholder="<?php echo $v['lang']->search; ?>" required autocomplete="off"/>
			</div>
			<div id="searchHelp">
				<a href="<?php echo BASEURL ?>page/help"><?php echo $v['lang']->searchHelp; ?></a>
			</div>
			<div id="searchBook" style="display:none;">
				<span ><?php echo $v['lang']->searchBook; ?></span> <span id="searchBookTxt"></span>
			</div>

			<div id="search_filter" class="close">
				<span><?php echo $v['lang']->filter; ?></span>
			</div>
			<div id="search_filter_block">

				<div id="search_category">
					<?php
						foreach($v['search']['categories'] as $key => $value)
							echo '<label style="white-space: nowrap"><input value="'.$key.'" type="checkbox" checked="checked"/>'.$v['search']['categoriesLang'][$key].'</label> ';
					?>
				</div>
				<div style="margin:15px;">
					<label><?php echo $v['lang']->from; ?> <input id="searchStart" type="number" placeholder="<?php echo $v['lang']->year; ?>"/></label>
					<label><?php echo $v['lang']->to; ?> <input id="searchEnd" type="number" placeholder="<?php echo $v['lang']->year; ?>"/></label>
				</div>

				<div style="margin:15px;">
					<label><?php echo $v['lang']->inIndex; ?> <input id="searchInIndex" type="checkbox"/></label>
				</div>
			</div>
			<div id="search_historic">
				<select id="searchHistoricSelect"><option value="null"><?php echo $v['lang']->historic; ?></option></select> <button id="clear"><?php echo $v['lang']->delete; ?></button>
			</div>
		</form>
</div>


<div id="search_frames">
	<div id="search_frame_select_source" class="framer paneselect">
			<div class="frame_title"><?php echo $v['lang']->source; ?></div><div id="search_sourceMsg" class="frame_subtitle"></div>
	</div>
	<div id="search_frame_select_nosource" class="framer">
		<div class="frame_title"><?php echo $v['lang']->nosource; ?></div><div id="search_nosourceMsg" class="frame_subtitle"></div>
	</div>
	<div id="search_frame_select_biblio" class="framer">
		<div class="frame_title"><?php echo $v['lang']->bibliography; ?></div><div id="search_biblioMsg" class="frame_subtitle"></div>
	</div>
</div>

<div id="publications">
	<div id="search_frame_source">
		<div>
			<div id="frieze"></div>
			<div id="frieze_caption">
				<span id="frieze_caption_occ"><?php echo $v['lang']->occMsg_p; ?></span>
				<span id="frieze_caption_book"><?php echo $v['lang']->book_p; ?></span>
				<span id="frieze_caption_year"><?php echo $v['lang']->year_p; ?></span>
			</div>
			<div>
				<table id="publications_source">
					<thead>
						<tr>
							<th style="width: 30px;"><?php echo $v['lang']->date; ?></th>
							<th style="width: 30px;"><?php echo $v['lang']->date; ?></th>
							<th style="width: 150px;"><?php echo $v['lang']->authors; ?></th>
							<th style="width: 500px;"><?php echo $v['lang']->title; ?></th>
							<th style="width: 150px;"><?php echo $v['lang']->editors; ?></th>
							<th style="width: 55px;"><?php echo $v['lang']->publication_date; ?></th>
						</tr>
					</thead>
					<tbody id="publicationsBody_source"></tbody>
				</table>
			</div>
		</div>
		<div id="occurrences_header_source" class="occurrences_header">
			<span style="width: 55px;"><?php echo $v['lang']->date; ?></span>
			<span style="width: 55px;"><?php echo $v['lang']->date; ?></span>
			<span style="width: 140px;"><?php echo $v['lang']->authors; ?></span>
			<span style="width: 330px;"><?php echo $v['lang']->title; ?></span>
			<span style="width: 145px;"><?php echo $v['lang']->editors; ?></span>
			<span style="width: 90px;"><?php echo $v['lang']->publication_date; ?></span>
		</div>
		<article class="occurrences" id="occurrences_source"></article>
	</div>
	<div id="search_frame_nosource" style="display:none;">
		<div>
			<table id="publications_nosource">
				<thead>
					<tr>
						<th style="width: 150px;"><?php echo $v['lang']->authors; ?></th>
						<th style="width: 500px;"><?php echo $v['lang']->title; ?></th>
						<th style="width: 150px;"><?php echo $v['lang']->editors; ?></th>
						<th style="width: 55px;"><?php echo $v['lang']->publication_date; ?></th>
					</tr>
				</thead>
				<tbody id="publicationsBody_nosource"></tbody>
			</table>
		</div>
		<div id="occurrences_header_nosource" class="occurrences_header">
			<span style="width: 150px;"><?php echo $v['lang']->authors; ?></span>
			<span style="width: 500px;"><?php echo $v['lang']->title; ?></span>
			<span style="width: 200px;"><?php echo $v['lang']->editors; ?></span>
			<span style="width: 55px;"><?php echo $v['lang']->publication_date; ?></span>
		</div>
		<article class="occurrences" id="occurrences_nosource"></article>
	</div>

	<div id="search_frame_biblio" style="display:none;">
		<div>
			<div>
				<table id="publications_biblio">
					<thead>
						<tr>
							<th style="width: 30px;"><?php echo $v['lang']->date; ?></th>
							<th style="width: 30px;"><?php echo $v['lang']->date; ?></th>
							<th style="width: 150px;"><?php echo $v['lang']->authors; ?></th>
							<th style="width: 500px;"><?php echo $v['lang']->title; ?></th>
							<th style="width: 150px;"><?php echo $v['lang']->editors; ?></th>
							<th style="width: 55px;"><?php echo $v['lang']->publication_date; ?></th>
						</tr>
					</thead>
					<tbody id="publicationsBody_biblio"></tbody>
				</table>
			</div>
		</div>
		<div id="occurrences_header_biblio" class="occurrences_header">
			<span style="width: 112px;"><?php echo $v['lang']->date; ?></span><span style="width: 151px;"><?php echo $v['lang']->authors; ?></span><span style="width: 451px;"><?php echo $v['lang']->title; ?></span><span style="width: 201px;"><?php echo $v['lang']->editors; ?></span><span style="width: 57px;"><?php echo $v['lang']->publication_date; ?></span>
		</div>
		<article class="occurrences" id="occurrences_biblio"></article>
	</div>

</div>
