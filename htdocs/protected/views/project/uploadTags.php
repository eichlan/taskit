<?php
$this->breadcrumbs = Utility::projectMenu( $project, false ) + array(
	"Upload Tags"
);
?><div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'upload-tags-form-uploadTags-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array(
		'enctype'=>'multipart/form-data',
	),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<p>To generate a tags file use something similar to this command:</p>
	<pre>ctags -f- --fields=fKlst src/*.cpp src/*.h | bzip2 -9 &gt; tags.bz2</pre>
	<p>Plain ctags output, as well as xz, bzip2, and gzip compressed ctags output are accepted, the filetype is determined by extension (.xz, .bz2, .gz)</p>
	<p>For now the fields parameter must match that provided above, in the future the parser may be more robust.</p>

	<div class="row">
		<?php echo $form->labelEx($model,'file'); ?>
		<?php echo $form->fileField($model,'file'); ?>
		<?php echo $form->error($model,'file'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
