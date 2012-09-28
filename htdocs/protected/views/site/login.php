<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<h1>Welcome to Taskit</h1>
<p>Taskit is an awesome Task management system with deep customization.  It's also free!<p>
<p>Taskit is currently in early development, we do not gurantee the safety of your data.</p>
<p>Taskit is on <a href="https://github.com/eichlan/taskit">GitHub</a>!  Check it out!</p>
<?php if( Yii::app()->params['anonInvites'] ) { ?>
<script type="text/javascript">
function newInv()
{
	$.ajax('<?php echo Yii::app()->createUrl('site/newInvite'); ?>', {
		dataType: 'json',
		success: function( data ) {
			if( 'error' in data ) {
				alert( data['error'] );
				return;
			}
//			$('#codes-left').text( data['count'] );
			alert('Your new invitation code is: ' + data['code'] +
				"\nEnter this on the Register page to create an account.");
		}
	});
}
</script>
<p>Taskit is free, but you need an invitation code to get in:</p>
<p><a href="javascript:void(0);" onclick="newInv()">Request an invitation</a>.</p>
<?php } ?>


<h1>Login</h1>

<p>Please fill out the following form with your login credentials:</p>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Login'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
