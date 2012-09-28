<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('teamid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->teamid), array('view', 'id'=>$data->teamid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ownerid')); ?>:</b>
	<?php echo CHtml::encode($data->ownerid); ?>
	<br />


</div>