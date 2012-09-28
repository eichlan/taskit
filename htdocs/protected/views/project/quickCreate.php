<?php
if( $parentid === null )
{
	print 'Creating a new project inside Team '.
		Team::model()->findByPk($teamid)->name;
}
else
{
	print 'Creating a new project inside project '.
		Project::model()->findByPk($parentid)->name. ' (in Team '.
		Team::model()->findByPk($teamid)->name.')';
}
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'project-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name'); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description'); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::ajaxSubmitButton('Submit', ''); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
