
<div id="start_novelty">
	<h1><?php echo $v['lang']->new; ?></h1>
	<table id="table_novelty">
		<thead>
			<tr>
			<th style="width: 30px;">[<?php echo $v['lang']->source_date; ?></th>
			<th style="width: 30px;"><?php echo $v['lang']->source_date; ?>]</th>
			<th style="width: 150px;"><?php echo $v['lang']->authors; ?></th>
			<th style="width: 450px;"><?php echo $v['lang']->title; ?></th>
			<th style="width: 200px;"><?php echo $v['lang']->editors; ?></th>
			<th style="width: 55px;"><?php echo $v['lang']->publication_date; ?></th>
			</tr>
		</thead>
		<tbody><?php echo $v['books']['novelty']; ?></tbody>
	</table>
</div>

<div class="panelsTabs" id="start_source">
	<div class="tabs">
		<div data-tab="source" class="tab"><div class="frame_title"><?php echo $v['lang']->source; ?></div></div>
		<div data-tab="nosource" class="tab"><div class="frame_title"><?php echo $v['lang']->nosource; ?></div></div>
	</div>
	<div class="panels">
		<div data-panel="source" class="panel">
			<table id="table_source">
				<thead>
					<tr>
						<th style="width: 30px;">[<?php echo $v['lang']->source_date; ?></th>
						<th style="width: 30px;"><?php echo $v['lang']->source_date; ?>]</th>
						<th style="width: 150px;"><?php echo $v['lang']->authors; ?></th>
						<th style="width: 450px;"><?php echo $v['lang']->title; ?></th>
						<th style="width: 200px;"><?php echo $v['lang']->editors; ?></th>
						<th style="width: 55px;"><?php echo $v['lang']->publication_date; ?></th>
					</tr>
				</thead>
				<tbody><?php echo $v['books']['source']; ?></tbody>
			</table>
		</div>
		<div data-panel="nosource" class="panel">
			<table id="table_nosource">
				<thead>
					<tr>
						<th style="width: 150px;"><?php echo $v['lang']->authors; ?></th>
						<th style="width: 500px;"><?php echo $v['lang']->title; ?></th>
						<th style="width: 200px;"><?php echo $v['lang']->editors; ?></th>
						<th style="width: 55px;"><?php echo $v['lang']->publication_date; ?></th>
					</tr>
				</thead>
				<tbody><?php echo $v['books']['nosource']; ?></tbody>
			</table>
		</div>
</div>
