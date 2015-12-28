<div data-panel="<?php echo $v['name']; ?>" class="panel">
	<table>
		<thead>
			<tr>
				<th style="width: 55px;"> </th>
				<th style="width: 55px;"><?php echo $v['lang']->id; ?></th>
				<th style="width: 150px;"><?php echo $v['lang']->authors; ?></th>
				<th style="width: 500px;"><?php echo $v['lang']->title; ?></th>
				<th style="width: 200px;"><?php echo $v['lang']->editors; ?></th>
				<th style="width: 55px;"><?php echo $v['lang']->date; ?></th>
			</tr>
		</thead>
		<tbody><?php echo $v['docsHTML']; ?></tbody>
	</table>
</div>
