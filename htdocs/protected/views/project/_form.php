<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'project-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'teamid'); ?>
		<?php echo $form->dropDownList($model,'teamid', CHtml::listData(
				$teams, 'teamid', 'name'
			),
			array(
				'ajax' => array(
					'type' => 'POST',
					'update' => '#Project_parentid',
					'url' => Yii::app()->createUrl('team/listProjects'),
				),
		)); ?>
		<?php echo $form->error($model,'teamid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'parentid'); ?>
		<?php echo $form->dropDownList($model,'parentid',$projects); ?>
		<?php echo $form->error($model,'parentid'); ?>
	</div>

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
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
