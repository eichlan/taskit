<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'task-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'typeid'); ?>
		<?php echo $form->dropDownList($model,'typeid',CHtml::listData(
			$project->team->teamTaskTypes, 'typeid', 'name'
		)); ?>
		<?php echo $form->error($model,'typeid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'statusid'); ?>
		<?php echo $form->dropDownList($model,'statusid',CHtml::listData(
			$project->team->teamTaskStatuses, 'statusid', 'name'
		)); ?>
		<?php echo $form->error($model,'statusid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'priority'); ?>
		<?php echo $form->dropDownList($model,'priority',CHtml::listData(
			$project->team->teamPriorities, 'priority', 'name'
		)); ?>
		<?php echo $form->error($model,'priority'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'complexity'); ?>
		<?php echo $form->dropDownList($model,'complexity',array(
			null=>'?', '1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4'
		)); ?>
		<?php echo $form->error($model,'complexity'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>80)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
