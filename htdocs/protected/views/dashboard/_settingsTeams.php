<script type="text/javascript">
function popUserPicker( teamid )
{
	$('#TeamAddUserButton').attr('teamid', teamid);
	$("#user-add-dlg").dialog("open");
}

function updateTeams()
{
	$.fn.yiiGridView.update('Teams');
}

function ajaxPostAddUser()
{
	$.ajax({
		type: 'POST',
		url: '<?php
			echo Yii::app()->createUrl('team/addUser', array('teamid'=>'XXX'))
			?>'.replace('XXX', $('#TeamAddUserButton').attr('teamid')),
		data: 'PickUserForm[name]='+
		$('input[name="PickUserForm[name]"]').val(),
		dataType: 'text',
		success: function(data,a,b){
			$("#user-add-dlg").dialog("close");
			updateTeams();
			return false;
		},
		
	});
	return false;
}
</script>
<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	'id'=>'user-add-dlg',
    'options'=>array(
        'title'=>'Add User to Team',
		'autoOpen'=>false,
		'modal'=>true,
    ),
)); ?>

<div class="form">

<?php 

$tmpModel = new PickUserForm;

$form=$this->beginWidget('CActiveForm', array(
	'id'=>'pick-user-form',
	'enableAjaxValidation'=>true,
	'action'=>'#',
	'htmlOptions'=>array(
		'onsubmit'=>"ajaxPostAddUser(); return false;",
	),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($tmpModel); ?>

	<div class="row">
		<?php echo $form->labelEx($tmpModel,'name'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>'PickUserForm[name]',
			'sourceUrl'=>Yii::app()->createUrl('team/userAutoComplete'),
			'options'=>array(
				'minLength' => 1,
			),
		)); ?>
		<?php echo $form->error($tmpModel,'name'); ?>
	</div>

<?php $this->endWidget(); ?>

	<div class="row buttons">
		<button id="TeamAddUserButton" onclick="ajaxPostAddUser(); return false;">Add</button>
	</div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>

<?php
function editUserMenu( $teamid )
{
	return '<a onclick="popUserPicker('.$teamid.');"><img src="/images/users.png" style="float: right;" /></a>';
}

$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider' => $teams,
	'id'=>'Teams',
	'columns' => array(
		'name',
		'description',
		array(
			'name'=>'members',
			'value'=>'Utility::userList($data->users).editUserMenu($data->teamid)',
			'type'=>'raw',
		),
	),
)); ?>
<a href="<?php echo Yii::app()->createUrl('team/create');
?>">Create a new team</a>
