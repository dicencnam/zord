<div class="blocklink" style="text-align:center;">
	<a href="<?php echo BASEURL ?>">
		<h1>demo</h1>
	</a>
</div>
<div id="lang_choise">
	<form name="userconnex" method="post"  action="<?php echo BASEURL; ?>">
		<input type="hidden" value="Start" name="module"/>
		<input type="hidden" value="lang" name="action"/>
	<?php
		include(CONFIG_FOLDER.'config_language.php');
		foreach ($zordLangsString as $key => $lang) {
			if($key==LANG)
				echo '<span class="lang-select">'.$lang.'</span>';
			else
				echo '<span class="lang-noselect"  data-lang="'.$key.'">'.$lang.'</span>';
		}
	?>
	</form>
</div>
