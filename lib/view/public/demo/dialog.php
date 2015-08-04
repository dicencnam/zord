<div style="display:none;">
	<div id="template_dialog_citation">
		<div class="dialog-box">
			<p class="dialog-title"><?php echo $v->comment; ?></p>
			<div class="dialog-content">
				<button data-id="dialog_citation_cancel" style="margin-top:0"><?php echo $v->cancel; ?></button>
				&#160;&#160;&#160;&#160;&#160;
				<button data-id="dialog_citation_ok" style="margin-top:0"><?php echo $v->ok; ?></button>
			<br/>
				<p class="dialog-subtitle" style="margin-top:30px"><?php echo $v->addnote; ?></p>
				<textarea data-id="dialog_citation_comment"></textarea><br/>
			</div>
		</div>
	</div>
	<div id="template_dialog_bug">
		<div class="dialog-box">
			<p class="dialog-title"><?php echo $v->bug; ?></p>
			<div class="dialog-content">
				<button data-id="dialog_bug_cancel" style="margin-top:0"><?php echo $v->cancel; ?></button>
				&#160;&#160;&#160;&#160;&#160;
				<button data-id="dialog_bug_ok" style="margin-top:0"><?php echo $v->ok; ?></button>
			<br/>
				<p class="dialog-subtitle" style="margin-top:30px"><?php echo $v->addnote; ?></p>
				<textarea data-id="dialog_bug_comment"></textarea><br/>
			</div>
		</div>
	</div>
	<div id="template_dialog_help">
		<div class="dialog-box">
			<p class="dialog-title"><?php echo $v->help; ?></p>
			<div class="dialog-content">
				<div class="dialog_help_content" data-id="content">
				</div>
				<button data-id="dialog_help_close" style="margin-top:0"><?php echo $v->close; ?></button>
			</div>
		</div>
	</div>
	<div id="template_dialog_citation_valid"><div class="dialog-box"><p class="waitmsg"><?php echo $v->add_citation; ?></p></div></div>
	<div id="template_dialog_bug_valid"><div class="dialog-box"><p class="waitmsg"><?php echo $v->bug_save; ?></p></div></div>
	<div id="template_dialog_bug_help"><?php echo $v->bug_help; ?></div>
	<div id="template_button_next"><?php echo $v->button_next; ?></div>
	<div id="template_button_before"><?php echo $v->button_before; ?></div>
</div>
