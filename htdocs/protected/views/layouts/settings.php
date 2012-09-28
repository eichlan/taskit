<?php $this->beginContent('//layouts/main'); ?>
<div class="yiiTab"><?php $this->widget('zii.widgets.CMenu', array(
	'items' => array(
		array('label'=>'Profile', 'url'=>array('settings/profile')),
		array('label'=>'Teams & Projects', 'url'=>array('settings/structure')),
		array('label'=>'Invitations', 'url'=>array('settings/invitations')),
	),
	'htmlOptions' => array(
		'class'=>'tabs',
	),
)); ?><div class="view">
		<?php echo $content; ?>
</div></div>
<?php $this->endContent(); ?>
