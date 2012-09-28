<?php Yii::app()->clientScript->registerScriptFile( 
	"/js/jquery.json-2.3.min.js",
	CClientScript::POS_HEAD
); ?>
<div class="form">
	<div class="row">
		<label for="title">Title</label>
		<div class="row-right">
			<input type="text" id="title" value="<?php echo htmlspecialchars($this->title); ?>" />
		</div>
	</div>
	
	<div class="row">
		<label for="editnowdef">Edit now checked by default</label>
		<div class="row-right">
			<input type="checkbox" id="editnowdef" <?php
				if( $this->editNowDef ) echo 'checked="checked"'; ?> />
		</div>
	</div>
	
	<div class="row">
		<label for="editnowshow">Show edit now checkbox</label>
		<div class="row-right">
			<input type="checkbox" id="editnowshow" <?php
				if( $this->editNowShow ) echo 'checked="checked"'; ?> />
			<p>If the "Edit now" checkbox is not shown, the default value (above) will always be used.</p>
		</div>
	</div>

<script type="text/javascript">
function go()
{
	saveWidget( $.toJSON({
		widget: 'QuickTask',
		params: {
			title: $('#title').val(),
			editNowDef: $('#editnowdef').prop('checked'),
			editNowShow: $('#editnowshow').prop('checked'),
		}
	}) );
}
</script>

	<div class="row buttons">
		<label>&nbsp;</label>
		<div class="row-right">
			<button onclick="go();">Save Widget</button>
		</div>
	</div>

	<div style="clear: both;"></div>
</div>
