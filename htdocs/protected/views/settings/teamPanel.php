<script type="text/javascript">
function popUserPicker( teamid )
{
	$("#PickUserForm_name").attr('value','');
	$("#user-add-dlg").dialog("open");
}

function ajaxPostAddUser()
{
	$.ajax({
		type: 'POST',
		url: '<?php
			echo Yii::app()->createUrl('team/addUser', array('teamid'=>$model->teamid)); ?>',
		data: 'PickUserForm[name]='+
		$('input[name="PickUserForm[name]"]').val(),
		dataType: 'text',
		success: function(data,a,b){
			reloadCurPanel();
			$("#user-add-dlg").dialog("close");
			return false;
		},
		
	});
	return false;
}
curTeamId = <?php echo $model->teamid; ?>;
curParentId = null;
</script>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'team-teamPanel-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'ownerid'); ?>
		<div class="row-right">
			<?php echo $model->owner->dispName(); ?>
			<?php echo $form->error($model,'ownerid'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<div class="row-right">
			<?php echo $form->textField($model,'name'); ?>
			<?php echo $form->error($model,'name'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<div class="row-right">
			<?php echo $form->textField($model,'description'); ?>
			<?php echo $form->error($model,'description'); ?>
		</div>
	</div>

	<div class="row">
		<label>Team Members</label>
		<div class="row-right">
			<?php echo Utility::userList($model->users); ?><br />
			<a href="javascript:void(0);" onclick="popUserPicker(<?php echo $model->teamid; ?>);">Add a member</a>
			<?php echo $form->error($model,'description'); ?>
		</div>
	</div>

	<div class="row buttons">
		<label>&nbsp;</label>
		<div class="row-right">
			<?php echo CHtml::submitButton('Submit'); ?>
		</div>
	</div>

	<div style="clear: both;"></div>

<?php $this->endWidget(); ?>

</div><!-- form -->
