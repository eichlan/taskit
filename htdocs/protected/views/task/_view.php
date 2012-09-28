<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('taskid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->taskid), array('view', 'id'=>$data->taskid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('authorid')); ?>:</b>
	<?php echo CHtml::encode($data->authorid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('projectid')); ?>:</b>
	<?php echo CHtml::encode($data->projectid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('milestoneid')); ?>:</b>
	<?php echo CHtml::encode($data->milestoneid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('priority')); ?>:</b>
	<?php echo CHtml::encode($data->priority); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('typeid')); ?>:</b>
	<?php echo CHtml::encode($data->typeid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?php echo CHtml::encode($data->created); ?>
	<br />

	*/ ?>

</div>