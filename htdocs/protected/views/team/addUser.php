<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'pick-user-form',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>'PickUserForm[name]',
			'sourceUrl'=>Yii::app()->createUrl('team/userAutoComplete'),
			'options'=>array(
				'minLength' => 1,
			),
		)); ?>
		<?php //echo $form->textField($model,'name'); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

<script type="text/javascript">
function ajaxPostAddUser()
{
	$.ajax({
		type: 'POST',
		url: '<?php
			echo Yii::app()->createUrl('team/addUser', array('teamid'=>$teamid))
			?>',
		data: 'ajax=pick-user-form&PickUserForm[name]='+
		$('input[name="PickUserForm[name]"]').val,
		success: function(data,a,b){
			updateTeams();
			$("#user-add-dlg").dialog("close");
			return false;
		},
		complete: function(x, st) { }
		
	});
	return false;
}
</script>
	<div class="row buttons">
		<?php /*$this->widget('zii.widgets.jui.CJuiButton',
	array(
		'name'=>'button',
		'caption'=>'Add',
		'value'=>'asd',
		'onclick'=>'js:function(){$.post(\''.
		Yii::app()->createUrl('team/addUser', array('teamid'=>$teamid)).
		'\',\'ajax=pick-user-form&PickUserForm[name]=\'.$form.find(\'input[name="PickUserForm[name]"]\'), function(data,a,b){updateTeams(); $("#user-add-dlg").dialog("close"); return false;}); return false;}',
		)
	); */?>
		<?php /* echo CHtml::ajaxSubmitButton('Submit',
			Yii::app()->createUrl('team/addUser', array('teamid'=>$teamid)),
			array('success'=>'function(data,a,b){updateTeams(); $("#user-add-dlg").dialog("close");}',
			'type'=>'POST')); */ ?>
	</div>

<?php $this->endWidget(); ?>

		<button onclick="ajaxPostAddUser(); return false;">Add</button>
</div><!-- form -->
