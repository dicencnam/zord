<div id="marker_styles">
	<div id="markers_buttons">
		<div class="markers_buttonswarp">
			<span id="marker_title"><?php echo $v['lang']->title ?></span>
		</div>
		<div style="width:290px;float:left">
			<button id="markers_clear" data-tooltip="<?php echo $v['lang']->clear_tooltip ?>"><?php echo $v['lang']->clear ?></button>
			<button id="markers_export" data-tooltip="<?php echo $v['lang']->export_tooltip ?>"><?php echo $v['lang']->export ?></button>
			<br/><br/>
			<span  data-tooltip="<?php echo $v['lang']->styles_tooltip ?>">
				<select id="marker_styles_select">
					<?php
					foreach ($v['csl'] as $key => $value)
						echo '<option value="'.$key.'">'.$value.'</option>';
					?>
				</select>
			</span>
		</div>
	</div>
	<div>
		<span class="help_dialog help_bubble_red"  style="margin-top:8px;"><div><?php echo $v['lang']->citation_help ?></div></span>
	</div>
</div>
<div id="markers"></div>
