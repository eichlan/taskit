<div class="misc-box-head">
Editing widget on <?php echo $viewArea; ?> view "<?php echo $viewTitle; ?>"
</div>
<div class="misc-box-body-foot">
<script type="text/javascript">
function saveWidget( jsonIn ) {
	$.ajax({
		type: 'POST',
		url: '<?php echo Yii::app()->createUrl('widget/save', array(
			'vid'=>$vid, 'wid'=>$wid, 'oid'=>$oid, 'eid'=>$eid,
			)); ?>',
		data: jsonIn,
		success: function(d){ window.location = d; },
		error: function(a,b,c){ alert(a+' '+b+' '+c); }
	});
}
</script>
<?php $this->widget('application.taskit.widgets.'.$desc['widget'],
	$desc['params'] + array('scenario'=>'edit')
); ?>
</div>
