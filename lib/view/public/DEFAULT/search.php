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

<div class="panelsTabs" id="search_frames">
	<div class="tabs">
		<div data-tab="source" class="tab">
			<div class="frame_title"><?php echo $v['lang']->source; ?></div><div id="dzFrameMsg_source" class="frame_subtitle"></div>
		</div>
		<div data-tab="nosource" class="tab">
			<div class="frame_title"><?php echo $v['lang']->nosource; ?></div><div id="dzFrameMsg_nosource" class="frame_subtitle"></div>
		</div>
	</div>

	<div class="panels" id="publications">
		<div>
			<div id="frieze"></div>
			<div id="frieze_caption">
				<span id="frieze_caption_occ"><?php echo $v['lang']->occMsg_p; ?></span>
				<span id="frieze_caption_book"><?php echo $v['lang']->book_p; ?></span>
				<span id="frieze_caption_year"><?php echo $v['lang']->year_p; ?></span>
			</div>
		</div>
		<div data-panel="source" class="panel">
			<div>
				<table id="dz_source">
					<thead id="dzHead_source">
						<tr class="head_sources">
							<th class="creation_date_i_Occ">[<?php echo $v['lang']->source_date; ?></th>
							<th class="creation_date_after_i_Occ"><?php echo $v['lang']->source_date; ?>]</th>
							<th class="creator_ss_Occ"><?php echo $v['lang']->authors; ?></th>
							<th class="title_s_Occ"><?php echo $v['lang']->title; ?></th>
							<th class="editor_ss_Occ"><?php echo $v['lang']->editors; ?></th>
							<th class="date_i_Occ"><?php echo $v['lang']->publication_date; ?></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div id="dzOccHead_source" class="occurrences_header head_sources">
				<span class="creation_date_i_Occ">[<?php echo $v['lang']->source_date; ?></span>
				<span class="creation_date_after_i_Occ"><?php echo $v['lang']->source_date; ?>]</span>
				<span class="creator_ss_Occ"><?php echo $v['lang']->authors; ?></span>
				<span class="title_s_Occ"><?php echo $v['lang']->title; ?></span>
				<span class="editor_ss_Occ"><?php echo $v['lang']->editors; ?></span>
				<span class="date_i_Occ"><?php echo $v['lang']->publication_date; ?></span>
			</div>
			<article class="occurrences head_sources" id="dzOcc_source"></article>
		</div>

		<div data-panel="nosource" class="panel">
			<div>
				<table id="dz_nosource">
					<thead id="dzHead_nosource">
						<tr class="head_nosources">
							<th class="creator_ss_Occ"><?php echo $v['lang']->authors; ?></th>
							<th class="title_s_Occ"><?php echo $v['lang']->title; ?></th>
							<th class="editor_ss_Occ"><?php echo $v['lang']->editors; ?></th>
							<th class="date_i_Occ"><?php echo $v['lang']->publication_date; ?></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div id="dzOccHead_nosource" class="occurrences_header head_nosources">
				<span class="creator_ss_Occ"><?php echo $v['lang']->authors; ?></span>
				<span class="title_s_Occ"><?php echo $v['lang']->title; ?></span>
				<span class="editor_ss_Occ"><?php echo $v['lang']->editors; ?></span>
				<span class="date_i_Occ"><?php echo $v['lang']->publication_date; ?></span>
			</div>
			<article class="occurrences head_nosources" id="dzOcc_nosource"></article>
		</div>

	</div>
</div>
