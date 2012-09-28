<?php
$this->breadcrumbs = array(
	'Profile',
);
?>
<script type="text/javascript">
function getNewAvatar()
{
	var link = $('#new-avatar-link');
	var txt = link.attr('onclick');
	link.removeAttr('onclick');
	link.removeAttr('href');
	$.ajax('<?php echo Yii::app()->createUrl('settings/getNewAvatar'); ?>', {
		success: function( d ) {
			var ap = $('#avatar-preview');
			ap.attr('src', '<?php
echo Yii::app()->createUrl('file/index', array(
	'cat'=>'avatar', 'id'=>Yii::app()->user->id, 'size'=>'avatar',
	'time'=>'XXX'
)); ?>'.replace('XXX', ''+((new Date()).getTime())) );
			link.attr('href', 'javascript:void(0);');
			link.attr('onclick', txt );
		}
	});
}
</script>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'profile-form-profile-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array(
		'enctype'=>'multipart/form-data',
	),	
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<div class="row-right">
			<?php echo $form->textField($model,'email'); ?>
			<?php echo $form->error($model,'email'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'timezone'); ?>
		<div class="row-right">
			<?php echo $form->dropDownList($model,'timezone', $tzList); ?>
			<?php echo $form->error($model,'timezone'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'realName'); ?>
		<div class="row-right">
			<?php echo $form->textField($model,'realName'); ?>
			<?php echo $form->error($model,'realName'); ?>
		</div>
	</div>

	<div class="row">
		<label>Current Avatar</label>
		<div class="row-right">
			<img id="avatar-preview" src="<?php echo Yii::app()->createUrl('file/index', array(
	'cat'=>'avatar', 'id'=>Yii::app()->user->id, 'size'=>'avatar'
)); ?>" alt="avatar" />
			<a id="new-avatar-link" href="javascript:void(0);" onclick="getNewAvatar();">Get a new avatar</a>
			<br />
		</div>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'avatar'); ?>
		<div class="row-right">
			<?php echo $form->fileField($model,'avatar'); ?>
			<?php echo $form->error($model,'avatar'); ?>
		</div>
	</div>

	<div class="row">
		<label>Password:</label>
		<div class="row-right">
			<a href="javascript:void(0);" onclick="$('#change-pw-dlg').dialog('open');">Change password</a>
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

<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	'id'=>'change-pw-dlg',
    'options'=>array(
        'title'=>'Change Password',
		'autoOpen'=>false,
		'modal'=>true,
    ),
)); ?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'change-password-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array(
		'enctype'=>'multipart/form-data',
	),	
)); ?>

	<div class="row">
		<?php echo $form->labelEx($chgpw,'password'); ?>
		<div class="row-right">
			<?php echo $form->passwordField($chgpw,'password'); ?>
			<?php echo $form->error($chgpw,'password'); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($chgpw,'password2'); ?>
		<div class="row-right">
			<?php echo $form->passwordField($chgpw,'password2'); ?>
			<?php echo $form->error($chgpw,'password2'); ?>
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
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>

