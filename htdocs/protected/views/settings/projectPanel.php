<script type="text/javascript">
curTeamId = <?php echo $model->teamid; ?>;
curParentId = <?php echo $model->projectid; ?>;
</script>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'project-projectPanel-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'open'); ?>
		<div class="row-right">
			<?php echo $form->checkBox($model,'open'); ?>
			<?php echo $form->error($model,'open'); ?>
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
			<?php echo $form->textArea($model,'description'); ?>
			<?php echo $form->error($model,'description'); ?>
		</div>
	</div>
	
	<div class="row">
		<label>Created:</label>
		<div class="row-right">
			<?php echo Utility::timeAgo( $model->created ); ?>
		</div>
	</div>

	<div class="row">
		<label>Last Modified:</label>
		<div class="row-right">
			<?php echo Utility::timeAgo( $model->updated ); ?>
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
